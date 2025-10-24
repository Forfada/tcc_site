// Cookie Manager Class
class CookieManager {
    constructor() {
        this.cookieConsent = this.getCookie('cookieConsent');
        this.setupEventListeners();
        this.checkCookieConsent();
    }

    setCookie(name, value, days) {
        let expires = '';
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + value + expires + '; path=/; SameSite=Strict; Secure';
    }

    getCookie(name) {
        const nameEQ = name + '=';
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    eraseCookie(name) {
        document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT; SameSite=Strict; Secure';
    }

    setupEventListeners() {
        // Botões do banner
        const acceptBannerBtn = document.getElementById('cookieAcceptBannerBtn');
        const manageBannerBtn = document.getElementById('cookieManageBtn');
        if (acceptBannerBtn) {
            acceptBannerBtn.addEventListener('click', () => this.acceptAllCookies());
        }
        if (manageBannerBtn) {
            manageBannerBtn.addEventListener('click', () => this.showCookieModal());
        }

        // Botões do modal
        const acceptModalBtn = document.getElementById('cookieAcceptModalBtn');
        const saveModalBtn = document.getElementById('cookieSaveModalBtn');
        if (acceptModalBtn) {
            acceptModalBtn.addEventListener('click', () => this.acceptAllCookies());
        }
        if (saveModalBtn) {
            saveModalBtn.addEventListener('click', () => this.savePreferences());
        }
    }

    checkCookieConsent() {
        if (!this.cookieConsent) {
            this.showCookieBanner();
        } else {
            this.hideCookieBanner();
            // Esconde o modal também se estiver aberto
            const modalElement = document.getElementById('cookieModal');
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            }
        }
    }

    showCookieBanner() {
        const banner = document.getElementById('cookieBanner');
        if (banner) banner.style.display = 'flex';
    }

    hideCookieBanner() {
        const banner = document.getElementById('cookieBanner');
        if (banner) banner.style.display = 'none';
        // Esconde o modal também
        const modalElement = document.getElementById('cookieModal');
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) modal.hide();
        }
    }

    showCookieModal() {
        const modalElement = document.getElementById('cookieModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    }

    acceptAllCookies() {
        this.setCookie('cookieConsent', 'all', 365);
        this.setCookie('analytics', 'true', 365);
        this.setCookie('marketing', 'true', 365);
        this.setCookie('preferences', 'true', 365);
        this.hideCookieBanner();
    }

    savePreferences() {
        const analytics = document.getElementById('analyticsCheck')?.checked;
        const marketing = document.getElementById('marketingCheck')?.checked;
        const preferences = document.getElementById('preferencesCheck')?.checked;

        this.setCookie('cookieConsent', 'custom', 365);
        this.setCookie('analytics', analytics ? 'true' : 'false', 365);
        this.setCookie('marketing', marketing ? 'true' : 'false', 365);
        this.setCookie('preferences', preferences ? 'true' : 'false', 365);
        this.hideCookieBanner();
    }
}

// Adicionar tratamento para o erro do img-bo-container
document.addEventListener('DOMContentLoaded', () => {
    // Inicializa o gerenciador de cookies
    new CookieManager();
    
    // Trata o img-bo-container com segurança
    const imgBoContainer = document.getElementById('img-bo-container');
    if (imgBoContainer) {
        const imgHtml = imgBoContainer.innerHTML;
        function toggleImage() {
            const screenWidth = window.innerWidth;
            if (screenWidth < 865) {
                const img = document.querySelector('.img-bo');
                if (img) img.remove();
            } else {
                if (!document.querySelector('.img-bo')) {
                    imgBoContainer.innerHTML = imgHtml;
                }
            }
        }
        toggleImage();
        window.addEventListener('resize', toggleImage);
    }
});