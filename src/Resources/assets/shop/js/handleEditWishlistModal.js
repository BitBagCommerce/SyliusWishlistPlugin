import { WishlistModal } from './wishlistModal';

const editWishlistBtns = document.querySelectorAll('[data-wishlist-edit-id]');
const wishlistFormName = 'edit_wishlist';

const setWishlistModal = () => {
    editWishlistBtns.forEach(btn => {
        btn.addEventListener('click', () => {   
            new WishlistModal(
                {
                    headerTitle: 'Choose new name for your wishlist',
                    wishlistFormName: wishlistFormName,
                    wishlistBodyContent: `
                        <input type="text" id="${wishlistFormName}_name" name="${wishlistFormName}_name[name]" required="required" class="wishlist-confirmation-modal__body--input" data-bb-target="input">
                        <div class="ui red pointing label validation-error hidden" data-bb-target="error">Please enter wishlist name.</div>
                    `
                },
                {
                    cancelAction: () => {},
                    performAction: async () => {
                        const form = document.querySelector(`#${wishlistFormName}`);
                        const formValue = form.querySelector(`#${wishlistFormName}_name`);
    
                        const wishlistId = btn.dataset.wishlistEditId;
                        const url = `/wishlists/${wishlistId}/edit`;
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
    })
};

const turnOnListener = () => {
    if (!editWishlistBtns) {
        return;
    }
    
    setWishlistModal();
};

turnOnListener();
