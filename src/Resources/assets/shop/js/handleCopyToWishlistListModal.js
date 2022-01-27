import { CreateCopyToWishlistsListModal } from './copyToWishlistsListModal'

const copyToWishlistBtn = document.querySelector('[data-bb-wl-list-modal-target="choose-wishlist-button"]')

const setAddWishlistModal = () => {
    copyToWishlistBtn.addEventListener('click', (e) => {
        e.preventDefault();
        new CreateCopyToWishlistsListModal(
            {},
            {
                cancelAction: () => {},
                performAction: async () => {
                    const form = e.target.closest('form')
                    const formData = new FormData(form);
                    const url = '/wishlist/{wishlistId}/copy/{destinedWishlistId}'
                    const config = {method: 'POST', body: formData.json}
                    
                    try {
                        const response = await fetch(url, config);
                        const data = await response.json();
                        console.log(data)
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
    if (!copyToWishlistBtn) {
        return;
    }
    setAddWishlistModal();
};

turnOnListener();

