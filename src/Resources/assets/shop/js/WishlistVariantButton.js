export class WishlistVariantButton {
    constructor(node) {
        if (!node?.nodeType) throw new Error("The first parameter must be a NodeElement")

        this.node = node;
        this._init();
    }

    _init() {
        this.node.addEventListener('click', event => this._addVariantToWishlist(event))
    }

    async _addVariantToWishlist(event) {
        event.preventDefault();

        const url = await this._getWishlistVariantUri();

        this._redirectToWishlist(url);
    }

    async _getWishlistVariantUri() {
        try {
            const form = this.node.closest('form');
            const data = new FormData(form);

            data.append(this.node.name, '')

            const response = await fetch(form.action, { method: 'POST', body: data })

            return await response.text();
        } catch (error) {
            console.error(error)
        }
    }

    _redirectToWishlist(path) {
        location.href = path;
    }
}

export default WishlistVariantButton;
