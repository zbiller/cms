<?php

return [
    'default_disk' => 'local',

    'ffmpeg.binaries' => env('FFMPEG_PATH', 'ffmpeg'),

    'ffmpeg.threads'  => 12,

    'ffprobe.binaries' => env('FFPROBE_PATH', 'ffprobe'),

    'timeout' => 3600,
];
