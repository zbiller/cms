<?php

return [
    'default_disk' => 'local',

    'ffmpeg.binaries' => env('FFMPEG_DRIVER', 'ffmpeg'),

    'ffmpeg.threads'  => 12,

    'ffprobe.binaries' => env('FFPROBE_DRIVER', 'ffprobe'),

    'timeout' => 3600,
];
