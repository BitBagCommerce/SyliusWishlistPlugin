import { RemoveWishlistModal } from './removeWishlistModal';

const removeWishlistBtns = document.querySelectorAll('[data-wishlist-remove-id]');

const setRemoveWishlistModal = () => {
    removeWishlistBtns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            new RemoveWishlistModal(
                {},
                {
                    cancelAction: () => {},
                    performAction: async () => {
                        const url = `/wishlists/${btn.dataset.wishlistRemoveId}/remove`
                        const csrfToken = document.querySelector("[data-bb-csrf]").dataset.bbCsrf
    
                        const headers = new Headers({
                            'X-CSRF-TOKEN': csrfToken
                        });
    
                        const requestConfig = {
                            method: 'POST',
                            headers: headers, 
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
                },
            ).init();
        })
    })
}

const turnOnListener = () => {
    if (!removeWishlistBtns) {
        return;
    }
    setRemoveWishlistModal();
};

turnOnListener();
