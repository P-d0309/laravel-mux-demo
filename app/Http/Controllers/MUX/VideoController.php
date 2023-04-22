<?php

namespace App\Http\Controllers\MUX;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use MuxPhp\Api\AssetsApi;
use MuxPhp\Api\DirectUploadsApi;
use MuxPhp\Configuration;
use MuxPhp\Models\CreateAssetRequest;
use MuxPhp\Models\CreateUploadRequest;
use MuxPhp\Models\InputSettings;
use MuxPhp\Models\PlaybackPolicy;

class VideoController extends Controller
{
    public function uploadVideo()
    {
        return view('upload-video');
    }

    public function storeVideoData(Request $request)
    {
        // Store the uploaded file locally first.

        $tempName = Storage::disk('public')->put('uploads/video', $request->video);
        $url = Storage::url($tempName);
        $url = asset($url);
        // $url = "https://storage.googleapis.com/muxdemofiles/mux-video-intro.mp4";

        // Initialize the process to upload the video

        $uploadedVideoURL = $this->uploadVideoFromLocalToMux($url);
    }

    private function uploadVideoFromLocalToMux($url)
    {
        // Get MUX client
        $muxAPiClient = $this->getMuxApiClient();

        // Upload video to MUX cloud storage
        $input = new InputSettings(["url" => $url]);
        $createAssetRequest = new CreateAssetRequest(["input" => $input, "playback_policy" => [PlaybackPolicy::_PUBLIC]]);

        $result = $muxAPiClient->createAsset($createAssetRequest);
        return $this->createMuxVideoUrl($result);
    }
    /**
     * Create MUX Config
     *
     * @return Configuration
     */
    private function getMuxConfig()
    {
        $config = Configuration::getDefaultConfiguration()
            ->setUsername(env('MUX_TOKEN_ID'))
            ->setPassword(env('MUX_TOKEN_SECRET'));
        return $config;
    }


    /**
     * Create MUX client
     *
     * @return AssetsApi
     */
    private function getMuxApiClient()
    {
        $config = $this->getMuxConfig();
        return new AssetsApi(
            new Client(),
            $config
        );
    }

    /**
     * get uploaded file stream URL
     *
     * @param [type] $result
     * @return string
     */
    private function  createMuxVideoUrl($result)
    {
        return "https://stream.mux.com/" . $result->getData()->getPlaybackIds()[0]->getId() . ".m3u8";
    }

    /**
     * get the mix config for the direct video Upload
     *
     * @return string
     */
    public function directVideoUploadConfig()
    {
        $config = $this->getMuxConfig();
        $uploadsApi = new DirectUploadsApi(
            new Client(),
            $config
        );

        $createAssetRequest = new CreateAssetRequest(["playback_policy" => [PlaybackPolicy::_PUBLIC]]);
        $createUploadRequest = new CreateUploadRequest(["timeout" => 3600, "new_asset_settings" => $createAssetRequest, "cors_origin" => "*"]);

        $uploadAPI = $uploadsApi->createDirectUpload($createUploadRequest);
        $data = $uploadAPI->getData();
        $id = $data->getId();
        $endPoint = $data->getUrl();
        return Response::json(['url' => $endPoint, 'id' => $id]);
    }

    public function getUploadVideoFromUploadId($id) {
        $config = $this->getMuxConfig();

        $uploadsApi = new DirectUploadsApi(
            new Client(),
            $config
        );

        try {
            $result = $uploadsApi->getDirectUpload($id);
            $uploadedAssetId = $result->getData()->getAssetId();
            $respose = $this->getUploadedAsset($uploadedAssetId);

        } catch (\Exception $e) {
            echo 'Exception when calling DirectUploadsApi->getDirectUpload: ', $e->getMessage(), PHP_EOL;
        }
    }

    public function getUploadedAsset($assetID) {
        $config = $this->getMuxConfig();

        $uploadsApi = new AssetsApi(
            new Client(),
            $config
        );

        try {
            $result = $uploadsApi->getAsset($assetID);
            $uploadVideoStreamURL = $this->createMuxVideoUrl($result);
            // You can store the URL in database, or you can also save ghe asset ID in database to perform further actions in the future.
            return $uploadVideoStreamURL;
        } catch (\Exception $e) {
            echo 'Exception when calling AssetsApi->getAsset: ', $e->getMessage(), PHP_EOL;
        }
    }
}
