import './handleWishlistMainCheckbox';
import './handleCopyToWishlistListModal';
import './handleAddAnotherWishlistModal';
import './handleRemoveWishlistModal';
import './handleEditWishlistModal';
import { WishlistVariantButton } from './WishlistVariantButton';
import { WishlistVariantPrice } from './WishlistVariantPrice';

const WishlistVariantElements = [...document.querySelectorAll('[data-bb-toggle="wishlist-variant"]')];
export const WishlistVariantButtonList = WishlistVariantElements.map(button => new WishlistVariantButton(button).init());

const WishlistVariantPrices = [...document.querySelectorAll('[data-bb-toggle="wishlist-variant-price"]')];
export const WishlistVariantPricesList = WishlistVariantPrices.map(price => new WishlistVariantPrice(price).init());

export default {
    WishlistVariantButtonList,
    WishlistVariantPricesList
};
