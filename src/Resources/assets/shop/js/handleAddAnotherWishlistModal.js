import { WishlistModal } from './wishlistModal';

const addWishlistBtn = document.querySelector('[data-bb-wishlist-add="add-another-wishlist"]');
const wishlistFormName = 'create_new_wishlist';

const setWishlistModal = () => {
    addWishlistBtn.addEventListener('click', () => {   
        new WishlistModal(
            {
                headerTitle: 'Choose name for your new wishlist',
                wishlistFormName: wishlistFormName,
                wishlistBodyContent: `
                    <input type="text" id="${wishlistFormName}_name" name="${wishlistFormName}[name]" required="required" class="wishlist-confirmation-modal__body--input" data-bb-target="input">
                    <div class="ui red pointing label validation-error hidden" data-bb-target="error">Please enter wishlist name.</div>
                `
            },
            {
                cancelAction: () => {},
                performAction: async () => {
                    const form = document.querySelector(`#${wishlistFormName}`);
                    const formValue = form.querySelector(`#${wishlistFormName}_name`);

                    const url = '/wishlists/create';
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
                        
                    } catch (error) {
                        console.error(error);
                    } finally {
                        location.reload();
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
