import { BulkAction } from './bulkAction';

const bulkExportToPdfButton = document.querySelectorAll('[data-wishlist-bulk-export-to-pdf]');
const bulkExportToPdf = () => {
    bulkExportToPdfButton.forEach(btn => {
        btn.addEventListener('click', () => {
            new BulkAction(
                {
                    cancelAction: () => {},
                    performAction: async () => {
                        const form = document.querySelector(`#wishlist_form`);

                        const wishlistId = btn.dataset.wishlistBulkExportToPdf;
                        const url = `/en_US/wishlist/${wishlistId}/export/pdf`;
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
                            const data = await response.json();
                            console.log(data)

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
    if (!bulkExportToPdfButton) {
        return;
    }

    bulkExportToPdf();
};

turnOnListener();
