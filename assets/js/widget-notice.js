(function (wp) {
    if (!wp || !wp.data || !wp.data.dispatch) return;

    const { dispatch } = wp.data;
    const disableUrl = wp.url ? wp.url.addQueryArgs('options-general.php', { page: 'twim-settings' }) : '/wp-admin/options-general.php?page=twim-settings';

    // Show notice once DOM is ready
    const showNotice = () => {
        dispatch('core/notices').createNotice(
            'warning',
            'Tiny Widget Manager will not be operational because the block-based widget editor is currently enabled. You can disable it in Widget Visibility Settings.',
            {
                isDismissible: true,
                actions: [
                    {
                        label: 'Open Settings',
                        url: disableUrl,
                    },
                ],
            }
        );
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showNotice);
    } else {
        showNotice();
    }
})(window.wp);
