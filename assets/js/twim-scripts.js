(function ($) {
    function initTwimWidgetControls(context) {
        $(context).find('.twim-widget-controls').filter(function () {
            if (context != document) return true;
            const $holder = $(this).closest('.widgets-holder-wrap');
            return !$holder.is('#available-widgets'); /* &&
                !$holder.hasClass('inactive-sidebar'); */
        }).each(function () {
            const $container = $(this);

            // Tabs
            $container.find('.twim-tab-nav li').off('click').on('click', function () {
                const tab = $(this).data('tab');
                $container.find('.twim-tab-nav li').removeClass('active');
                $(this).addClass('active');
                $container.find('.twim-tab-content').removeClass('active').hide();
                $container.find(`.twim-tab-content[data-tab="${tab}"]`).addClass('active').show();
            });

            // Selectize with dropdown list
            $container.find('.twim-selectize').each(function () {
                if (context != document && this.selectize) {
                    this.selectize.destroy();
                }
                if (!this.selectize) {
                    $(this).selectize({
                        plugins: ['remove_button'],
                        delimiter: ',',
                        persist: false,
                        render: {
                            option: function (data, escape) {
                                const level = Number(data.level) || 0;
                                const indent = '&nbsp;'.repeat(level * 4);
                                return `<div class="option">${indent}${escape(data.text)}</div>`;
                            }
                        }
                    });
                }
            });

            // Selectize single
            $container.find('.twim-selectize-showhide').each(function () {
                if (context != document && this.selectize) {
                    this.selectize.destroy();
                }
                if (!this.selectize) {
                    $(this).selectize({
                        // plugins: ['remove_button'],
                        delimiter: ',',
                        persist: false,
                        render: {
                            option: function (data, escape) {
                                let icon = '';
                                if (data.value === 'show') {
                                    icon = '<span class="dashicons dashicons-visibility" style="margin-right:4px;"></span>';
                                } else if (data.value === 'hide') {
                                    icon = '<span class="dashicons dashicons-hidden" style="margin-right:4px;"></span>';
                                }
                                return '<div style="vertical-align:middle">' + icon + escape(data.text) + '</div>';
                            },
                            item: function (data, escape) {
                                let icon = '';
                                if (data.value === 'show') {
                                    icon = '<span class="dashicons dashicons-visibility" style="margin-right:4px;"></span>';
                                } else if (data.value === 'hide') {
                                    icon = '<span class="dashicons dashicons-hidden" style="margin-right:4px;"></span>';
                                }
                                return '<div style="vertical-align:middle">' + icon + escape(data.text) + '</div>';
                            }
                        }
                    });
                }
            });

            // Active tab on load
            $container.find('.twim-tab-nav li.active').trigger('click');
        });

    }

    $(document).ready(function () {
        initTwimWidgetControls(document);
    });

    $(document).on('widget-updated widget-added', function (event, widget) {
        initTwimWidgetControls(widget);
    });


    /* ----------------------------------------------------------------------------------------------------------------*/
    /*                                          Disable logic if "disable" chosen
    /* ----------------------------------------------------------------------------------------------------------------*/
    function toggleTwimWrap($select) {
        var $wrap = $select.closest('.twim-tabs').find('.twim-wrap');
        if (($select.val() === 'show') || ($select.val() === 'hide')) {
            $wrap.addClass('twim-disabled');
        } else {
            $wrap.removeClass('twim-disabled');
        }
    }

    $('.twim-andor').each(function () {
        toggleTwimWrap($(this));
    });

    $(document).on('change', '.twim-andor', function () {
        toggleTwimWrap($(this));
    });


    /* ----------------------------------------------------------------------------------------------------------------*/
    /*                                                 STORE LAST ACTIVE NAV TAB
    /* ----------------------------------------------------------------------------------------------------------------*/

    let isSavingWidget = false;

    $(document).on('click', '.widget-control-save', function () {
        isSavingWidget = true;

        // Optional: clear flag after short delay, or after widget-updated
        setTimeout(() => {
            isSavingWidget = false;
        }, 2000); // adjust if needed
    });

    // Store selected tab on click
    $(document).on('click', '.twim-tab-nav li', function () {
        // Prevent active tab saving if on widget save event
        if (isSavingWidget) {
            console.log('Tab click ignored during widget save');
            return;
        }

        const $li = $(this);
        const tab = $li.data('tab'); // or use text() or attr()
        const $widget = $li.closest('.widget');
        $widget.data('twim-active-tab', tab);
    });

    function restoreTabState($widget) {
        const activeTab = $widget.data('twim-active-tab');
        if (activeTab) {
            $widget.find('.twim-tab-nav li').removeClass('active');
            $widget.find('.twim-tab-nav li[data-tab="' + activeTab + '"]').addClass('active');
            $widget.find('.twim-tab-content').hide();
            $widget.find('.twim-tab-content[data-tab="' + activeTab + '"]').show();
        }
    }

    $('.widget').each(function () {
        const widget = this;

        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (
                    mutation.type === 'attributes' &&
                    mutation.attributeName === 'class'
                ) {
                    const wasDirty = mutation.oldValue.includes('widget-dirty');
                    const isDirtyNow = widget.classList.contains('widget-dirty');

                    if (wasDirty && !isDirtyNow) {
                        restoreTabState($(widget));
                    }
                }
            });
        });

        observer.observe(widget, {
            attributes: true,
            attributeFilter: ['class'],
            attributeOldValue: true
        });
    });


})(jQuery);
