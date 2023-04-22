<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Upload Video') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <h4 class="text-lg text-white mb-4">Upload via server</h4>
				<form action="{{ route('storeVideoData') }}" method="post" class="bg-white shadow-md rounded p-6 mb-4" enctype="multipart/form-data">
					@csrf
					<div class="mb-4">
						<label class="block text-gray-700 text-sm font-bold mb-2" for="username">
							Video
						</label>
						<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="video" name="video" type="file" placeholder="Username">
					</div>
					<div class="flex items-center justify-between">
						<button
							class="bg-blue-500 text-dark font-bold py-2 px-4 rounded "
							type="submit">
							Upload
						</button>
					</div>
				</form>
                <h4 class="text-lg text-white mb-4">Upload via AJAX</h4>
                <form action="#" class="bg-white shadow-md rounded p-6 mb-4" enctype="multipart/form-data" id="browser-upload">
					@csrf
					<div class="mb-4">
						<label class="block text-gray-700 text-sm font-bold mb-2" for="username">
							Video
						</label>
						<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="video-browser" name="video-browser" type="file" placeholder="Username">
					</div>
					<div class="flex items-center justify-between">
						<button
							class="bg-blue-500 text-dark font-bold py-2 px-4 rounded "
							type="submit">
							Upload
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-app-layout>

@vite(['resources/js/upload.js'])
