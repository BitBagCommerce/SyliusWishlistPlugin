import { AddWishlistModal } from './addAnotherWishlistModal'

const addWishlistBtn = document.querySelector('[data-bb-wishlist-add="add-another-wishlist"]')

const setAddWishlistModal = () => {
    
    addWishlistBtn.addEventListener('click', (e) => {   
        e.preventDefault();
        new AddWishlistModal(
            {},
            {
                cancelAction: () => {},
                performAction: async () => {
                    const form = document.querySelector('#create_new_wishlist')

                    const url = '/wishlists/create'
                    const formData = new FormData(form);
                    const csrfToken = document.querySelector("[data-bb-csrf]").dataset.bbCsrf

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
                        location.reload()
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
    setAddWishlistModal();
};

turnOnListener();

