import axios from "axios";
import * as UpChunk from "@mux/upchunk";

$(document).on("submit", "#browser-upload", async function (e) {
    e.preventDefault();
    const video = document.getElementById('video-browser');
    const response = await axios.get("/direct-video-upload-config");
    const url = response.data.url;
    const id = response.data.id;

    const upload = UpChunk.createUpload({
        endpoint:url,
        file: video.files[0],
        chunkSize: 5120, // Uploads the file in ~5mb chunks
    });

    // subscribe to events
    upload.on("error", (err) => {
        console.error("💥 🙀", err.detail);
    });

    upload.on("progress", (progress) => {
        console.log("Uploaded", progress.detail, "percent of this file.");
    });

    // subscribe to events
    upload.on("success", (success) => {
        // get laravel route in JS using ziggy librabry. https://github.com/tighten/ziggy
        const getAssetIDRoute = route("getUploadVideoFromUploadId", id);
        console.log("🚀 ~ file: upload.js:30 ~ upload.on ~ getAssetIDRoute:", getAssetIDRoute)
        axios.get(getAssetIDRoute).then((res) => console.log(res)).catch((e) => alert(e));
    });
});
