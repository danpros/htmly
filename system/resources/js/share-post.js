var copyPermalinkToastTimeout;

function showCopyPermalinkToast(message) {
    var toast = document.getElementById('copy-permalink-toast');

    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'copy-permalink-toast';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        toast.style.position = 'fixed';
        toast.style.bottom = '16px';
        toast.style.right = '16px';
        toast.style.zIndex = '9999';
        toast.style.background = 'rgba(0, 0, 0, 0.82)';
        toast.style.color = '#fff';
        toast.style.padding = '8px 12px';
        toast.style.borderRadius = '4px';
        toast.style.fontSize = '14px';
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 160ms ease-in-out';
        document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.style.opacity = '1';

    if (copyPermalinkToastTimeout) {
        clearTimeout(copyPermalinkToastTimeout);
    }

    copyPermalinkToastTimeout = setTimeout(function() {
        toast.style.opacity = '0';
    }, 1800);
}

function copyPermalink(url) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function() {
            showCopyPermalinkToast('Permalink copied to clipboard.');
        }).catch(function() {
            window.prompt('Copy this permalink:', url);
            showCopyPermalinkToast('Clipboard access blocked. Copy from prompt.');
        });
    } else {
        window.prompt('Copy this permalink:', url);
        showCopyPermalinkToast('Clipboard not available. Copy from prompt.');
    }

    return false;
}

function shareToFediverse(platformName, encodedText) {
    var input = window.prompt('Enter your ' + platformName + ' server domain (for example: mastodon.social)');
    if (!input) {
        return false;
    }

    var domain = input.trim().replace(/^https?:\/\//i, '').replace(/\/+$/, '');
    if (!domain) {
        return false;
    }

    var shareUrl = 'https://' + domain + '/share?text=' + encodedText;
    window.open(shareUrl, '_blank', 'noopener,noreferrer');
    return false;
}