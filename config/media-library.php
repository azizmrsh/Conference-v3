<?php

return [
    /*
     * The disk on which to store added files and derived images by default.
     */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * The maximum file size of an item in bytes.
     * Adding a larger file will result in an exception.
     */
    'max_file_size' => 1024 * 1024 * 20, // 20MB

    /*
     * This queue will be used to generate derived images.
     */
    'queue_name' => env('MEDIA_QUEUE', ''),

    /*
     * By default all conversions will be performed on a queue.
     */
    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', false),

    /*
     * The fully qualified class name of the media model.
     */
    'media_model' => \Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * The fully qualified class name of the model used for temporary uploads.
     */
    'temporary_upload_model' => \Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * When enabled, media collections will be serialised using the default
     * serialisation method.
     */
    'enable_temporary_uploads_session_affinity' => true,

    /*
     * When enabled, media library will generate thumbnails for uploaded images.
     */
    'generate_thumbnails_for_temporary_uploads' => true,

    /*
     * The class that contains the strategy for determining a media file's path.
     */
    'path_generator' => \Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,

    /*
     * The class that contains the strategy for determining how to add files to the media library.
     */
    'file_namer' => \Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer::class,

    /*
     * The class that contains the strategy for determining a media file's url.
     */
    'url_generator' => \Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    /*
     * Whether to activate versioning when urls are generated.
     */
    'version_urls' => false,

    /*
     * The media library will try to optimize all converted images by removing
     * metadata and applying a little bit of compression.
     */
    'image_optimizers' => [
        \Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
            '-m85',
            '--force',
            '--strip-all',
            '--all-progressive',
        ],
        \Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
            '--force',
        ],
        \Spatie\ImageOptimizer\Optimizers\Optipng::class => [
            '-i0',
            '-o2',
            '-quiet',
        ],
        \Spatie\ImageOptimizer\Optimizers\Svgo::class => [
            '--disable=cleanupIDs',
        ],
        \Spatie\ImageOptimizer\Optimizers\Gifsicle::class => [
            '-b',
            '-O3',
        ],
        \Spatie\ImageOptimizer\Optimizers\Cwebp::class => [
            '-m 6',
            '-pass 10',
            '-mt',
            '-q 90',
        ],
    ],

    /*
     * These generators will be used to create an image of media files.
     */
    'image_generators' => [
        \Spatie\MediaLibrary\Conversions\ImageGenerators\Image::class,
        \Spatie\MediaLibrary\Conversions\ImageGenerators\Webp::class,
        \Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf::class,
        \Spatie\MediaLibrary\Conversions\ImageGenerators\Svg::class,
        \Spatie\MediaLibrary\Conversions\ImageGenerators\Video::class,
    ],

    /*
     * The engine that should perform the image conversions.
     * Should be either `gd` or `imagick`.
     */
    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    /*
     * FFMPEG & FFProbe binaries paths, only used if you try to generate video
     * thumbnails and have installed the php-ffmpeg/php-ffmpeg composer dependency.
     */
    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    /*
     * The path where to store temporary files while performing image conversions.
     */
    'temporary_directory_path' => null,

    /*
     * Here you can override the class names of the jobs used by this package.
     */
    'jobs' => [
        'perform_conversions' => \Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
        'generate_responsive_images' => \Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
    ],

    /*
     * Image loading attribute which will be added to images rendered by the `getResponsiveImageUrls` function.
     * Possible values: 'auto', 'eager', 'lazy', null
     */
    'default_loading_attribute_value' => null,

    'responsive_images' => [
        /*
         * This class is responsible for calculating the target widths of the responsive
         * images. By default we optimize for filesize and create variations that each are 30%
         * smaller than the previous one. More info in the documentation.
         *
         * https://docs.spatie.be/laravel-medialibrary/v9/advanced-usage/generating-responsive-images
         */
        'width_calculator' => \Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,

        /*
         * By default rendering media to a responsive image will add some javascript and a tiny placeholder.
         * This ensures that the browser can already determine the correct layout.
         */
        'use_tiny_placeholders' => true,

        /*
         * This class will generate the tiny placeholder used for progressive image loading.
         */
        'tiny_placeholder_generator' => \Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],

    /*
     * When enabling this option, a route will be registered that will enable
     * the Media Library Pro UI.
     */
    'enable_vapor_uploads' => env('ENABLE_MEDIA_LIBRARY_VAPOR_UPLOADS', false),

    /*
     * When converting Media instances to response, media library will add
     * a `loading` attribute to the `img` tag. Here you can set the default value.
     *
     * Possible values: 'auto', 'lazy', 'eager', null
     */

    /*
     * You can specify a prefix for that is used for storing all media.
     * If you set this to `/my-subdir`, all your media will be stored in a `/my-subdir` directory.
     */
    'prefix' => env('MEDIA_PREFIX', ''),
];
