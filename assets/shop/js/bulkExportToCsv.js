import { BulkAction } from './bulkAction';

const bulkExportToCsvButton = document.querySelectorAll('[data-wishlist-bulk-export-to-csv]');
const bulkExportToCsv = () => {
    bulkExportToCsvButton.forEach(btn => {
        btn.addEventListener('click', () => {
            new BulkAction(
                {
                    cancelAction: () => {},
                    performAction: async () => {
                        const form = document.querySelector(`#wishlist_form`);

                        const wishlistId = btn.dataset.wishlistBulkExportToCsv;
                        const url = `${window.location.href}/csv/export`;
                        const formData = new FormData(form);
                        const csrfToken = document.querySelector("[data-bb-csrf]").dataset.bbCsrf;

                        const headers = new Headers({
                            'X-CSRF-TOKEN': csrfToken
                        });

                        const requestConfig = {
                            method: 'POST',
                            headers: headers,
                            body: formData
                        }

                        try {
                            const response = await fetch(url, requestConfig);
                            const blob = await response.blob();
                            const downloadUrl = URL.createObjectURL(blob);

                            window.open(downloadUrl, '_blank');

                        } catch (error) {
                            console.error(error);
                        } finally {
                            location.reload();
                        }
                    },
                }
            ).init();
        });
    })
};

const turnOnListener = () => {
    if (!bulkExportToCsvButton) {
        return;
    }

    bulkExportToCsv();
};

turnOnListener();
