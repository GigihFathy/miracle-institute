@php
    $appName = config('app.name', 'Miracle Institute');
    $defaultTitle = $appName;
    $defaultDescription = 'Miracle Institute adalah platform pembelajaran untuk bertumbuh dalam iman melalui topik pembelajaran, materi, dan pertemuan belajar yang terarah.';

    $resolvedTitle = trim((string) ($seoTitle ?? $__env->yieldContent('title', $defaultTitle)));
    $resolvedDescription = trim((string) ($seoDescription ?? $__env->yieldContent('meta_description', $defaultDescription)));
    $resolvedKeywords = trim((string) ($seoKeywords ?? $__env->yieldContent('meta_keywords', 'Miracle Institute, topik pembelajaran iman, pembelajaran Kristen, pertumbuhan rohani, belajar Alkitab')));
    $resolvedImage = $seoImage ?? asset('images/banner-seo.png');
    $resolvedUrl = $seoUrl ?? url()->current();
    $resolvedType = $seoType ?? 'website';
    $resolvedRobots = $seoRobots ?? 'index, follow';
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $appName,
        'url' => url('/'),
        'logo' => asset('images/logo.png'),
        'image' => $resolvedImage,
        'description' => $resolvedDescription,
    ];
@endphp

<title>{{ $resolvedTitle }}</title>
<meta name="description" content="{{ $resolvedDescription }}">
<meta name="keywords" content="{{ $resolvedKeywords }}">
<meta name="robots" content="{{ $resolvedRobots }}">
<link rel="canonical" href="{{ $resolvedUrl }}">

<meta property="og:locale" content="id_ID">
<meta property="og:type" content="{{ $resolvedType }}">
<meta property="og:site_name" content="{{ $appName }}">
<meta property="og:title" content="{{ $resolvedTitle }}">
<meta property="og:description" content="{{ $resolvedDescription }}">
<meta property="og:url" content="{{ $resolvedUrl }}">
<meta property="og:image" content="{{ $resolvedImage }}">
<meta property="og:image:alt" content="{{ $resolvedTitle }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $resolvedTitle }}">
<meta name="twitter:description" content="{{ $resolvedDescription }}">
<meta name="twitter:image" content="{{ $resolvedImage }}">

<script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
