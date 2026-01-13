/**
 * Keks Cookie Banner JavaScript
 * Stores consent in localStorage (no cookie before consent!)
 * Supports granular cookie categories
 */

(function() {
    'use strict';

    const STORAGE_KEY = 'keks_consent';

    const Keks = {
        previewMode: false,
        requireConsent: true,
        showBlockOverlay: false,
        categories: {},
        granularMode: true,
        strings: {},

        /**
         * Initialize
         */
        init: function() {
            // Check preview mode (?keks_preview=1)
            const urlParams = new URLSearchParams(window.location.search);
            this.previewMode = urlParams.get('keks_preview') === '1';

            // Load config
            if (typeof keksConfig !== 'undefined') {
                this.requireConsent = keksConfig.requireConsent !== false;
                this.showBlockOverlay = keksConfig.showBlockOverlay || false;
                this.categories = keksConfig.categories || {};
                this.granularMode = keksConfig.granularMode !== false;
                this.strings = keksConfig.strings || {};
            }

            const consent = this.getConsent();

            // Check if page should be blocked (not on privacy/imprint pages)
            // wp_localize_script converts: true -> "1", false -> ""
            const shouldBlock = typeof keksConfig !== 'undefined' && keksConfig.shouldBlockPage;

            if (this.previewMode) {
                console.log('🍪 ' + this.strings.previewMode);
                this.showBanner();
                if (this.requireConsent && shouldBlock) {
                    this.blockPage();
                }
                if (this.showBlockOverlay && shouldBlock) {
                    this.displayOverlay();
                }
            } else if (consent === null) {
                // No decision made yet - only show banner on non-excluded pages
                if (shouldBlock) {
                    this.showBanner();
                    if (this.requireConsent) {
                        this.blockPage();
                    }
                    if (this.showBlockOverlay) {
                        this.displayOverlay();
                    }
                }
            } else if (!this.hasRequiredConsent(consent) && this.requireConsent) {
                // No sufficient consent and consent required - only on non-excluded pages
                if (shouldBlock) {
                    this.showBanner();
                    this.blockPage();
                    if (this.showBlockOverlay) {
                        this.displayOverlay();
                    }
                }
            }

            this.bindEvents();

            // If consent exists: activate allowed scripts
            if (consent && !this.previewMode) {
                this.activateAllowedScripts();
            }
        },

        /**
         * Check if sufficient consent was given
         */
        hasRequiredConsent: function(consent) {
            if (!consent || !consent.categories) return false;
            // At least necessary must be true
            return consent.categories.necessary === true;
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            document.addEventListener('click', (e) => {
                // Accept all
                if (e.target.classList.contains('keks-btn-accept')) {
                    e.preventDefault();
                    if (!this.previewMode) {
                        this.acceptAll();
                    }
                    this.hideBanner();
                    this.unblockPage();
                    this.hideOverlay();
                    if (this.previewMode) {
                        console.log('🍪 ' + this.strings.acceptedPreview);
                    }
                }

                // Reject all (only visible when requireConsent = false)
                if (e.target.classList.contains('keks-btn-reject')) {
                    e.preventDefault();
                    if (!this.previewMode) {
                        this.rejectAll();
                    }
                    this.hideBanner();
                    this.unblockPage();
                    this.hideOverlay();
                    if (this.previewMode) {
                        console.log('🍪 ' + this.strings.rejectedPreview);
                    }
                }

                // Save selection
                if (e.target.classList.contains('keks-btn-save')) {
                    e.preventDefault();
                    if (!this.previewMode) {
                        this.saveSelection();
                    }
                    // Necessary cookies are always required, so selection.necessary is always true
                    this.hideBanner();
                    this.unblockPage();
                    this.hideOverlay();
                    if (this.previewMode) {
                        console.log('🍪 ' + this.strings.selectionSavedPreview, this.getSelection());
                    }
                }

                // Revoke link
                if (e.target.classList.contains('keks-revoke-link')) {
                    e.preventDefault();
                    this.revokeConsent();
                }
            });
        },

        /**
         * Read current selection from checkboxes
         */
        getSelection: function() {
            const selection = {};
            const checkboxes = document.querySelectorAll('#keks-banner input[data-category]');

            checkboxes.forEach(cb => {
                const category = cb.dataset.category;
                // Required categories are always true
                if (this.categories[category] && this.categories[category].required) {
                    selection[category] = true;
                } else {
                    selection[category] = cb.checked;
                }
            });

            // If no checkboxes found (non-granular mode), activate all
            if (Object.keys(selection).length === 0) {
                Object.keys(this.categories).forEach(key => {
                    selection[key] = this.categories[key].required || false;
                });
            }

            return selection;
        },

        /**
         * Accept all categories
         */
        acceptAll: function() {
            const categories = {};
            Object.keys(this.categories).forEach(key => {
                categories[key] = true;
            });
            this.setConsent(categories, 'accept_all');
        },

        /**
         * Reject all optional categories
         */
        rejectAll: function() {
            const categories = {};
            Object.keys(this.categories).forEach(key => {
                categories[key] = this.categories[key].required || false;
            });
            this.setConsent(categories, 'reject_all');
        },

        /**
         * Save current selection
         */
        saveSelection: function() {
            const selection = this.getSelection();
            this.setConsent(selection, 'custom');
        },

        /**
         * Show banner
         */
        showBanner: function() {
            const banner = document.getElementById('keks-banner');
            if (banner) {
                banner.classList.remove('keks-hidden');
                requestAnimationFrame(() => {
                    banner.classList.add('keks-visible');
                });

                // Load saved selection into checkboxes
                const consent = this.getConsent();
                if (consent && consent.categories) {
                    const checkboxes = banner.querySelectorAll('input[data-category]');
                    checkboxes.forEach(cb => {
                        const category = cb.dataset.category;
                        if (consent.categories[category] !== undefined && !cb.disabled) {
                            cb.checked = consent.categories[category];
                        }
                    });
                }
            }
        },

        /**
         * Hide banner
         */
        hideBanner: function() {
            const banner = document.getElementById('keks-banner');
            if (banner) {
                banner.classList.remove('keks-visible');
                setTimeout(() => {
                    banner.classList.add('keks-hidden');
                }, 300);
            }
        },

        /**
         * Show blocking overlay (when enabled)
         */
        displayOverlay: function() {
            if (document.getElementById('keks-block-overlay')) {
                document.getElementById('keks-block-overlay').classList.add('keks-visible');
                return;
            }

            const overlay = document.createElement('div');
            overlay.id = 'keks-block-overlay';
            overlay.className = 'keks-block-overlay';

            const message = (typeof keksConfig !== 'undefined' && keksConfig.requireConsentMessage)
                ? keksConfig.requireConsentMessage
                : this.strings.overlayMessage;

            overlay.innerHTML = `
                <div class="keks-block-content">
                    <div class="keks-block-icon">🍪</div>
                    <p class="keks-block-message">${message}</p>
                    <p class="keks-block-hint">${this.strings.overlayHint}</p>
                </div>
            `;

            document.body.appendChild(overlay);
            requestAnimationFrame(() => {
                overlay.classList.add('keks-visible');
            });
        },

        /**
         * Hide blocking overlay
         */
        hideOverlay: function() {
            const overlay = document.getElementById('keks-block-overlay');
            if (overlay) {
                overlay.classList.remove('keks-visible');
                setTimeout(() => {
                    overlay.remove();
                }, 300);
            }
        },

        /**
         * Block page (prevent scrolling and clicks)
         */
        blockPage: function() {
            document.body.classList.add('keks-body-blocked');

            // Create transparent overlay (behind banner, above page)
            if (!document.getElementById('keks-page-blocker')) {
                const blocker = document.createElement('div');
                blocker.id = 'keks-page-blocker';
                blocker.className = 'keks-page-blocker';
                document.body.appendChild(blocker);
            }
        },

        /**
         * Unblock page
         */
        unblockPage: function() {
            document.body.classList.remove('keks-body-blocked');
            const blocker = document.getElementById('keks-page-blocker');
            if (blocker) {
                blocker.remove();
            }
        },

        /**
         * Read consent from localStorage
         */
        getConsent: function() {
            try {
                const data = localStorage.getItem(STORAGE_KEY);
                if (!data) return null;

                const parsed = JSON.parse(data);

                // Check if consent is expired
                if (parsed.expires && new Date(parsed.expires) < new Date()) {
                    localStorage.removeItem(STORAGE_KEY);
                    return null;
                }

                // Migration from old format
                if (parsed.accepted !== undefined && !parsed.categories) {
                    return {
                        categories: {
                            necessary: true,
                            statistics: parsed.accepted,
                            marketing: parsed.accepted
                        },
                        timestamp: parsed.timestamp,
                        expires: parsed.expires
                    };
                }

                return parsed;
            } catch (e) {
                return null;
            }
        },

        /**
         * Generate or retrieve consent ID
         */
        getConsentId: function() {
            try {
                let consentId = localStorage.getItem('keks_consent_id');
                if (!consentId) {
                    consentId = 'keks_' + Date.now() + '_' + Math.random().toString(36).slice(2, 11);
                    localStorage.setItem('keks_consent_id', consentId);
                }
                return consentId;
            } catch (e) {
                return 'keks_' + Date.now() + '_' + Math.random().toString(36).slice(2, 11);
            }
        },

        /**
         * Update Google Consent Mode v2
         */
        updateGoogleConsentMode: function(categories) {
            // Only if Google Consent Mode is enabled
            if (typeof keksConfig === 'undefined' || !keksConfig.googleConsentMode) {
                return;
            }

            // Check if gtag is available
            if (typeof gtag !== 'function') {
                return;
            }

            const consent = {
                'ad_storage': categories.marketing ? 'granted' : 'denied',
                'ad_user_data': categories.marketing ? 'granted' : 'denied',
                'ad_personalization': categories.marketing ? 'granted' : 'denied',
                'analytics_storage': categories.statistics ? 'granted' : 'denied'
            };

            gtag('consent', 'update', consent);
            console.log('🍪 ' + this.strings.gcmUpdated, consent);
        },

        /**
         * Save consent
         */
        setConsent: function(categories, action) {
            const days = (typeof keksConfig !== 'undefined' && keksConfig.consentDays)
                ? keksConfig.consentDays
                : 365;

            const expires = new Date();
            expires.setDate(expires.getDate() + days);

            const data = {
                categories: categories,
                timestamp: new Date().toISOString(),
                expires: expires.toISOString()
            };

            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            } catch (e) {
                console.warn(this.strings.localStorageUnavailable);
            }

            // Update Google Consent Mode v2
            this.updateGoogleConsentMode(categories);

            // Log server-side (for GDPR proof)
            if (typeof keksConfig !== 'undefined' && keksConfig.ajaxUrl && action) {
                const formData = new URLSearchParams();
                formData.append('action', 'keks_log_consent');
                formData.append('nonce', keksConfig.nonce);
                formData.append('consent_id', this.getConsentId());
                formData.append('consent_action', action);
                formData.append('categories', JSON.stringify(categories));
                formData.append('url', window.location.href);

                fetch(keksConfig.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData
                }).catch(err => {
                    console.warn(this.strings.loggingFailed, err);
                });
            }

            // Activate allowed scripts
            Object.keys(categories).forEach(category => {
                if (categories[category] === true) {
                    this.activateScripts(category);
                }
            });

            // Trigger custom event
            const event = new CustomEvent('keksConsentChanged', {
                detail: categories
            });
            document.dispatchEvent(event);

            console.log('🍪 ' + this.strings.consentSaved, categories);
        },

        /**
         * Revoke consent and show banner again
         */
        revokeConsent: function() {
            // Log revocation server-side
            if (typeof keksConfig !== 'undefined' && keksConfig.ajaxUrl) {
                const formData = new URLSearchParams();
                formData.append('action', 'keks_log_consent');
                formData.append('nonce', keksConfig.nonce);
                formData.append('consent_id', this.getConsentId());
                formData.append('consent_action', 'revoke');
                formData.append('categories', JSON.stringify({}));
                formData.append('url', window.location.href);

                fetch(keksConfig.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData
                }).catch(err => {
                    console.warn(this.strings.revokeFailed, err);
                });
            }

            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch (e) {
                // Ignore
            }

            // Reset Google Consent Mode (all denied)
            this.updateGoogleConsentMode({
                necessary: true,
                statistics: false,
                marketing: false
            });

            this.showBanner();
            // Only block page if allowed (not on privacy/imprint pages)
            const shouldBlock = typeof keksConfig !== 'undefined' && keksConfig.shouldBlockPage;
            if (this.requireConsent && shouldBlock) {
                this.blockPage();
            }
            if (this.showBlockOverlay && shouldBlock) {
                this.displayOverlay();
            }
        },

        /**
         * Check if a category is allowed
         */
        isAllowed: function(category) {
            const consent = this.getConsent();
            if (!consent || !consent.categories) return false;
            return consent.categories[category] === true;
        },

        /**
         * Check if optional cookies are allowed (compatibility)
         */
        isAccepted: function() {
            const consent = this.getConsent();
            if (!consent || !consent.categories) return false;
            // True if at least one optional category is allowed
            return Object.keys(consent.categories).some(key => {
                return key !== 'necessary' && consent.categories[key] === true;
            });
        },

        /**
         * Return all consent values as object
         */
        getCategories: function() {
            const consent = this.getConsent();
            if (!consent || !consent.categories) return {};
            return consent.categories;
        },

        /**
         * Activate blocked scripts for a category
         * Scripts must have type="text/plain" and data-keks-category="category"
         */
        activateScripts: function(category) {
            const scripts = document.querySelectorAll(`script[type="text/plain"][data-keks-category="${category}"]`);

            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');

                // Copy all attributes except type and data-keks-category
                Array.from(oldScript.attributes).forEach(attr => {
                    if (attr.name !== 'type' && attr.name !== 'data-keks-category') {
                        newScript.setAttribute(attr.name, attr.value);
                    }
                });

                // Copy inline script content
                if (oldScript.innerHTML) {
                    newScript.innerHTML = oldScript.innerHTML;
                }

                // Replace old script with new one
                oldScript.parentNode.replaceChild(newScript, oldScript);

                console.log(`🍪 ${this.strings.scriptActivated} (${category})`);
            });

            // Also activate iframes (e.g., YouTube, Google Maps)
            const iframes = document.querySelectorAll(`iframe[data-keks-category="${category}"][data-keks-src]`);
            iframes.forEach(iframe => {
                iframe.src = iframe.dataset.keksSrc;
                iframe.removeAttribute('data-keks-src');
                console.log(`🍪 ${this.strings.iframeActivated} (${category})`);
            });
        },

        /**
         * Activate all allowed scripts
         */
        activateAllowedScripts: function() {
            const consent = this.getConsent();
            if (!consent || !consent.categories) return;

            Object.keys(consent.categories).forEach(category => {
                if (consent.categories[category] === true) {
                    this.activateScripts(category);
                }
            });
        }
    };

    // Make globally available
    window.Keks = Keks;

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Keks.init());
    } else {
        Keks.init();
    }

})();
