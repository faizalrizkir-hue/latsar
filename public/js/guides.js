(function () {
    const PDF_WORKER_SRC = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    const MOBILE_QUERY = '(max-width: 820px)';

    function initGuideFlipbook() {
        const page = document.querySelector('[data-guides-page]');
        if (!page || page.dataset.guidesInitialized === '1') {
            return;
        }

        page.dataset.guidesInitialized = '1';

        const selectors = Array.from(page.querySelectorAll('[data-guide-selector]'));
        const reader = page.querySelector('[data-guide-reader]');
        const toolbar = page.querySelector('[data-guide-toolbar]');
        const bookStage = page.querySelector('[data-guide-book-stage]');
        const book = page.querySelector('[data-guide-book]');
        const embedPreview = page.querySelector('[data-guide-embed-preview]');
        const processPanel = page.querySelector('[data-guide-process-panel]');
        const nativePreview = page.querySelector('[data-guide-native-preview]');
        const emptyState = page.querySelector('[data-guide-empty]');
        const loading = page.querySelector('[data-guide-loading]');
        const titleNode = page.querySelector('[data-guide-current-title]');
        const audienceNode = page.querySelector('[data-guide-current-audience]');
        const descriptionNode = page.querySelector('[data-guide-current-description]');
        const openLink = page.querySelector('[data-guide-open-link]');
        const currentPageNode = page.querySelector('[data-guide-page-current]');
        const totalPageNode = page.querySelector('[data-guide-page-total]');
        const zoomLabel = page.querySelector('[data-guide-zoom-label]');
        const prevButton = page.querySelector('[data-guide-prev]');
        const nextButton = page.querySelector('[data-guide-next]');
        const zoomOutButton = page.querySelector('[data-guide-zoom-out]');
        const zoomInButton = page.querySelector('[data-guide-zoom-in]');
        const leftCanvas = page.querySelector('[data-guide-canvas-left]');
        const rightCanvas = page.querySelector('[data-guide-canvas-right]');
        const leftShell = page.querySelector('[data-guide-page-shell="left"]');
        const rightShell = page.querySelector('[data-guide-page-shell="right"]');
        const leftLabel = page.querySelector('[data-guide-left-label]');
        const rightLabel = page.querySelector('[data-guide-right-label]');
        const emptyTitle = page.querySelector('[data-guide-empty-title]');
        const emptyMessage = page.querySelector('[data-guide-empty-message]');

        if (!reader || selectors.length === 0) {
            return;
        }

        let pdfDocument = null;
        let activeGuide = null;
        let currentPage = 1;
        let totalPages = 0;
        let zoom = 1;
        let renderRun = 0;
        let isRendering = false;
        let hasLoadedGuide = false;
        let transitionRun = 0;

        const mediaQuery = window.matchMedia(MOBILE_QUERY);
        const reducedMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        const delay = (duration) => new Promise((resolve) => window.setTimeout(resolve, duration));

        const setText = (node, value) => {
            if (node) {
                node.textContent = value || '';
            }
        };

        const setHidden = (node, hidden) => {
            if (node) {
                node.hidden = hidden;
            }
        };

        const setLoading = (value) => {
            setHidden(loading, !value);
            reader.classList.toggle('is-loading', value);
        };

        const spreadSize = () => (mediaQuery.matches ? 1 : 2);

        const clampPage = (value) => {
            const step = spreadSize();
            const maxStart = step === 1 ? totalPages : Math.max(1, totalPages - ((totalPages + 1) % 2));
            return Math.min(Math.max(1, value), maxStart);
        };

        const setControlsEnabled = (enabled) => {
            [prevButton, nextButton, zoomOutButton, zoomInButton].forEach((button) => {
                if (button) {
                    button.disabled = !enabled;
                }
            });
        };

        const runReaderTransition = async (callback, shouldAnimate) => {
            const runId = ++transitionRun;
            const animate = shouldAnimate && !reducedMotionQuery.matches;

            if (!animate) {
                await callback();
                hasLoadedGuide = true;
                return;
            }

            reader.classList.remove('is-switching-in');
            reader.classList.add('is-transitioning', 'is-switching-out');
            setControlsEnabled(false);

            await delay(150);
            if (runId !== transitionRun) {
                return;
            }

            await callback();
            if (runId !== transitionRun) {
                return;
            }

            reader.classList.remove('is-switching-out');
            reader.classList.add('is-switching-in');
            hasLoadedGuide = true;

            await delay(240);
            if (runId !== transitionRun) {
                return;
            }

            reader.classList.remove('is-switching-in', 'is-transitioning');
            updateControls();
        };

        const updateControls = () => {
            const hasDocument = Boolean(pdfDocument && totalPages > 0);
            const step = spreadSize();
            const currentEnd = Math.min(totalPages, currentPage + step - 1);

            setText(currentPageNode, hasDocument ? (step === 1 ? currentPage : `${currentPage}-${currentEnd}`) : '0');
            setText(totalPageNode, hasDocument ? totalPages : '0');
            setText(zoomLabel, `${Math.round(zoom * 100)}%`);

            if (prevButton) {
                prevButton.disabled = !hasDocument || currentPage <= 1 || isRendering;
            }

            if (nextButton) {
                nextButton.disabled = !hasDocument || currentEnd >= totalPages || isRendering;
            }

            if (zoomOutButton) {
                zoomOutButton.disabled = !hasDocument || zoom <= 0.7 || isRendering;
            }

            if (zoomInButton) {
                zoomInButton.disabled = !hasDocument || zoom >= 1.4 || isRendering;
            }
        };

        const clearCanvas = (canvas) => {
            if (!canvas) {
                return;
            }

            const context = canvas.getContext('2d');
            context.clearRect(0, 0, canvas.width || 1, canvas.height || 1);
            canvas.width = 0;
            canvas.height = 0;
        };

        const showEmpty = (title, message) => {
            pdfDocument = null;
            renderRun++;
            totalPages = 0;
            currentPage = 1;
            clearCanvas(leftCanvas);
            clearCanvas(rightCanvas);
            setHidden(bookStage, true);
            setHidden(embedPreview, true);
            setHidden(processPanel, true);
            setHidden(nativePreview, true);
            setHidden(toolbar, true);
            setHidden(emptyState, false);
            if (embedPreview) {
                embedPreview.innerHTML = '';
            }
            setText(emptyTitle, title);
            setText(emptyMessage, message);
            if (nativePreview) {
                nativePreview.removeAttribute('src');
            }
            setLoading(false);
            updateControls();
        };

        const showNativePreview = (url) => {
            pdfDocument = null;
            renderRun++;
            totalPages = 0;
            currentPage = 1;
            setHidden(bookStage, true);
            setHidden(embedPreview, true);
            setHidden(processPanel, true);
            setHidden(toolbar, true);
            setHidden(emptyState, true);
            if (embedPreview) {
                embedPreview.innerHTML = '';
            }
            if (nativePreview) {
                nativePreview.src = url;
                setHidden(nativePreview, false);
            }
            setLoading(false);
            updateControls();
        };

        const setGuideHeader = (button) => {
            activeGuide = button;
            const title = button.dataset.guideTitle || 'Panduan';
            const audience = button.dataset.guideAudience || 'Panduan';
            const description = button.dataset.guideDescription || '';
            const embedUrl = button.dataset.guideEmbedUrl || '';
            const url = embedUrl || button.dataset.guideUrl || '';
            const mode = button.dataset.guideMode || 'guide';
            const readerMode = mode === 'process' ? audience : (embedUrl ? (button.dataset.guideEmbedProvider || 'Online Flipbook') : audience);

            setText(titleNode, title);
            setText(audienceNode, readerMode);
            setText(descriptionNode, description);

            if (openLink) {
                if (url) {
                    openLink.href = url;
                    openLink.textContent = mode === 'process' ? 'Lihat Gambar' : (embedUrl ? 'Buka Flipbook' : 'Buka PDF');
                    openLink.hidden = false;
                } else {
                    openLink.hidden = true;
                    openLink.removeAttribute('href');
                }
            }
        };

        const setActiveSelector = (button) => {
            selectors.forEach((selector) => {
                const isActive = selector === button;
                selector.classList.toggle('is-active', isActive);
                selector.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        };

        const showEmbedPreview = (button) => {
            const embedUrl = button.dataset.guideEmbedUrl || '';
            if (!embedPreview || !embedUrl) {
                return false;
            }

            pdfDocument = null;
            renderRun++;
            totalPages = 0;
            currentPage = 1;
            clearCanvas(leftCanvas);
            clearCanvas(rightCanvas);
            setHidden(bookStage, true);
            setHidden(toolbar, true);
            setHidden(processPanel, true);
            setHidden(nativePreview, true);
            setHidden(emptyState, true);
            setHidden(embedPreview, false);
            if (nativePreview) {
                nativePreview.removeAttribute('src');
            }

            embedPreview.innerHTML = '';
            const shell = document.createElement('div');
            shell.className = 'guide-embed-preview__shell';

            const frameWrap = document.createElement('div');
            frameWrap.className = 'guide-embed-aspect';

            const frame = document.createElement('iframe');
            frame.className = 'guide-embed-frame';
            frame.src = embedUrl;
            frame.title = button.dataset.guideEmbedTitle || button.dataset.guideTitle || 'Flipbook panduan';
            frame.loading = 'lazy';
            frame.referrerPolicy = 'strict-origin-when-cross-origin';
            frame.allowFullscreen = true;
            frame.setAttribute('seamless', 'seamless');
            frame.setAttribute('scrolling', 'no');
            frame.setAttribute('frameborder', '0');
            frame.setAttribute('allowtransparency', 'true');

            frameWrap.appendChild(frame);
            shell.appendChild(frameWrap);
            embedPreview.appendChild(shell);

            setLoading(false);
            updateControls();

            return true;
        };

        const showProcessPanel = () => {
            pdfDocument = null;
            renderRun++;
            totalPages = 0;
            currentPage = 1;
            clearCanvas(leftCanvas);
            clearCanvas(rightCanvas);
            setHidden(bookStage, true);
            setHidden(toolbar, true);
            setHidden(embedPreview, true);
            setHidden(nativePreview, true);
            setHidden(emptyState, true);
            setHidden(processPanel, false);
            if (embedPreview) {
                embedPreview.innerHTML = '';
            }
            if (nativePreview) {
                nativePreview.removeAttribute('src');
            }
            setLoading(false);
            updateControls();
        };

        const renderPage = async (pageNumber, canvas, shell, label, runId) => {
            if (!pdfDocument || !canvas || !shell || !label || pageNumber > totalPages) {
                if (canvas) {
                    clearCanvas(canvas);
                }
                if (shell) {
                    shell.classList.add('is-empty');
                }
                setText(label, '');
                return;
            }

            shell.classList.remove('is-empty');
            setText(label, `Halaman ${pageNumber}`);

            const pdfPage = await pdfDocument.getPage(pageNumber);
            if (runId !== renderRun) {
                return;
            }

            const unscaledViewport = pdfPage.getViewport({ scale: 1 });
            const shellWidth = Math.max(260, shell.clientWidth - 28);
            const deviceScale = window.devicePixelRatio || 1;
            const renderScale = Math.min(2.4, Math.max(0.65, (shellWidth / unscaledViewport.width) * zoom * deviceScale));
            const viewport = pdfPage.getViewport({ scale: renderScale });
            const context = canvas.getContext('2d');

            canvas.width = Math.floor(viewport.width);
            canvas.height = Math.floor(viewport.height);
            canvas.style.width = `${Math.floor(viewport.width / deviceScale)}px`;
            canvas.style.height = `${Math.floor(viewport.height / deviceScale)}px`;

            await pdfPage.render({ canvasContext: context, viewport }).promise;
        };

        const renderSpread = async (direction) => {
            if (!pdfDocument) {
                updateControls();
                return;
            }

            const runId = ++renderRun;
            isRendering = true;
            updateControls();
            setLoading(true);
            book.classList.remove('is-turning-next', 'is-turning-prev');
            if (direction) {
                book.classList.add(direction === 'prev' ? 'is-turning-prev' : 'is-turning-next');
            }

            try {
                const step = spreadSize();
                currentPage = clampPage(currentPage);
                await renderPage(currentPage, leftCanvas, leftShell, leftLabel, runId);
                await renderPage(step === 1 ? totalPages + 1 : currentPage + 1, rightCanvas, rightShell, rightLabel, runId);
            } catch (error) {
                showNativePreview(activeGuide?.dataset.guideUrl || '');
                return;
            } finally {
                if (runId === renderRun) {
                    isRendering = false;
                    setLoading(false);
                    window.setTimeout(() => {
                        book.classList.remove('is-turning-next', 'is-turning-prev');
                    }, 220);
                    updateControls();
                }
            }
        };

        const loadGuide = async (button) => {
            const shouldAnimate = hasLoadedGuide && activeGuide !== button;

            await runReaderTransition(async () => {
                setActiveSelector(button);
                setGuideHeader(button);

                const url = button.dataset.guideUrl || '';
                const embedUrl = button.dataset.guideEmbedUrl || '';
                const mode = button.dataset.guideMode || 'guide';
                const title = button.dataset.guideTitle || 'Panduan';
                const preferredFile = button.dataset.guidePreferredFile || 'panduan.pdf';

                if (mode === 'process') {
                    showProcessPanel();
                    return;
                }

                if (embedUrl && showEmbedPreview(button)) {
                    return;
                }

                if (!url) {
                    showEmpty(
                        `File panduan ${title} belum tersedia`,
                        `Tambahkan file PDF ${preferredFile} ke folder public/uploads/panduan, lalu buka ulang halaman ini.`
                    );
                    return;
                }

                setHidden(emptyState, true);
                setHidden(embedPreview, true);
                setHidden(processPanel, true);
                setHidden(nativePreview, true);
                setHidden(bookStage, false);
                setHidden(toolbar, false);
                if (embedPreview) {
                    embedPreview.innerHTML = '';
                }
                if (nativePreview) {
                    nativePreview.removeAttribute('src');
                }

                if (!window.pdfjsLib) {
                    showNativePreview(url);
                    return;
                }

                try {
                    setControlsEnabled(false);
                    setLoading(true);
                    window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDF_WORKER_SRC;
                    const loadingTask = window.pdfjsLib.getDocument({ url });
                    pdfDocument = await loadingTask.promise;
                    totalPages = pdfDocument.numPages || 0;
                    currentPage = 1;
                    zoom = 1;

                    if (totalPages <= 0) {
                        showNativePreview(url);
                        return;
                    }

                    await renderSpread();
                } catch (error) {
                    showNativePreview(url);
                }
            }, shouldAnimate);
        };

        prevButton?.addEventListener('click', () => {
            if (!pdfDocument || isRendering) {
                return;
            }
            currentPage = clampPage(currentPage - spreadSize());
            renderSpread('prev');
        });

        nextButton?.addEventListener('click', () => {
            if (!pdfDocument || isRendering) {
                return;
            }
            currentPage = clampPage(currentPage + spreadSize());
            renderSpread('next');
        });

        zoomOutButton?.addEventListener('click', () => {
            if (!pdfDocument || isRendering) {
                return;
            }
            zoom = Math.max(0.7, Number((zoom - 0.1).toFixed(1)));
            renderSpread();
        });

        zoomInButton?.addEventListener('click', () => {
            if (!pdfDocument || isRendering) {
                return;
            }
            zoom = Math.min(1.4, Number((zoom + 0.1).toFixed(1)));
            renderSpread();
        });

        selectors.forEach((selector) => {
            selector.addEventListener('click', () => loadGuide(selector));
        });

        document.addEventListener('keydown', (event) => {
            if (!page.contains(document.activeElement) && !page.matches(':hover')) {
                return;
            }

            if (event.key === 'ArrowLeft') {
                prevButton?.click();
            }

            if (event.key === 'ArrowRight') {
                nextButton?.click();
            }
        });

        mediaQuery.addEventListener?.('change', () => {
            if (!pdfDocument || isRendering) {
                return;
            }
            currentPage = clampPage(currentPage);
            renderSpread();
        });

        loadGuide(selectors.find((selector) => selector.classList.contains('is-active')) || selectors[0]);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGuideFlipbook);
    } else {
        initGuideFlipbook();
    }

    document.addEventListener('livewire:navigated', initGuideFlipbook);
})();
