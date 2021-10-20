import { WishlistVariantButton } from './WishlistVariantButton';
import { WishlistVariantPrice } from './WishlistVariantPrice';

const WishlistVariantElements = [...document.querySelectorAll('[data-bb-toggle="wishlist-variant"]')];
export const WishlistVariantButtonList = WishlistVariantElements.map(button => new WishlistVariantButton(button))

const WishlistVariantPrices = [...document.querySelectorAll('[data-bb-toggle="wishlist-variant-price"]')];
export const WishlistVariantPricesList = WishlistVariantPrices.map(price => new WishlistVariantPrice(price).init())

export default {
    WishlistVariantButtonList,
    WishlistVariantPricesList
};