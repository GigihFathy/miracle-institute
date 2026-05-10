@once
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <style>
        .ql-font-inter { font-family: 'Inter', sans-serif; }

        .article-prose .ql-editor h1 { font-size: 2em; font-weight: bold; margin-bottom: 0.5em; }
        .article-prose .ql-editor h2 { font-size: 1.5em; font-weight: bold; margin-bottom: 0.5em; }
        .article-prose .ql-editor h3 { font-size: 1.17em; font-weight: bold; margin-bottom: 0.5em; }

        .article-prose .ql-editor p { margin-bottom: 1rem; }
        .article-prose .ql-editor ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
        .article-prose .ql-editor ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }

        .article-prose .ql-editor .ql-align-center { text-align: center; }
        .article-prose .ql-editor .ql-align-right { text-align: right; }
        .article-prose .ql-editor .ql-align-justify { text-align: justify; }

        .article-prose .ql-editor li { margin-bottom: 0.25rem; }

        .article-prose .ql-editor img {
            max-width: 100%;
            height: auto;
            border-radius: 1rem;
            margin: 1rem 0;
        }

        .article-prose .ql-editor blockquote {
            border-left: 4px solid #0f172a;
            padding-left: 1rem;
            margin: 1.25rem 0;
            color: #334155;
            font-style: italic;
        }
    </style>
@endpush
@endonce