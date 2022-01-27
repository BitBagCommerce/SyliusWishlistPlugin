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
                    const config = {method: 'POST', body: formData}

                    try {
                        const response = await fetch(url, config);
                        // const data = await response.json();
                        console.log(response);
                    } catch (error) {
                        console.error(error);
                    } finally {
                        
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

