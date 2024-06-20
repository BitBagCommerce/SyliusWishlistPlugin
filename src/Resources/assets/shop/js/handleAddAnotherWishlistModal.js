import {WishlistModal} from './wishlistModal';

const addWishlistBtn = document.querySelector('[data-bb-wishlist-add="add-another-wishlist"]');
const wishlistFormName = 'create_new_wishlist';

const setWishlistModal = () => {
    addWishlistBtn.addEventListener('click', () => {
        new WishlistModal(
            {
                headerTitle: document.querySelector("[data-bb-wishlist-add-title]").dataset.bbWishlistAddTitle,
                cancelText: document.querySelector("[data-bb-wishlist-add-cancel]").dataset.bbWishlistAddCancel,
                performText: document.querySelector("[data-bb-wishlist-add-perform]").dataset.bbWishlistAddPerform,
                wishlistFormName: wishlistFormName,
                wishlistBodyContent: `
                    <input type="text" id="${wishlistFormName}_name" name="${wishlistFormName}[name]" required="required" class="wishlist-confirmation-modal__body--input" data-bb-target="input" maxlength="50">
                    <div class="ui red pointing label validation-error hidden" data-bb-target="error">${document.querySelector("[data-bb-wishlist-add-error]").dataset.bbWishlistAddError}</div>
                `
            },
            {
                cancelAction: () => {},
                performAction: async () => {
                    const form = document.querySelector(`#${wishlistFormName}`);
                    const formValue = form.querySelector(`#${wishlistFormName}_name`);

                    const url = document.querySelector("[data-bb-wishlist-add-url]").dataset.bbWishlistAddUrl;
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
                        window.location.href = `${window.location.origin}${data.url}`;

                    } catch (error) {
                        console.error(error);
                    }
                },
            }
        ).init();
    });
};

const turnOnListener = () => {
    if (!addWishlistBtn) {
        return;
    }

    setWishlistModal();
};

turnOnListener();
