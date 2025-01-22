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
                        const url = `/en_US/wishlist/${wishlistId}/csv/export`;
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
    if (!bulkExportToCsvButton) {
        return;
    }

    bulkExportToCsv();
};

turnOnListener();
