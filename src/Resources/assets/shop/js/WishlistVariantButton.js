export class WishlistVariantButton {
    #node

    constructor(node) {
        if (!node?.nodeType) throw new Error("The first parameter must be a NodeElement")
        this.#node = node;

        this.#init();

        return this
    }

    #init() {
        this.#node.addEventListener('click', event => this.#addVariantToWishlist(event))
    }

    async #addVariantToWishlist(event) {
        event.preventDefault();

        const url = await this.#getWishlistVariantUri();

        this.#redirectToWishlist(url);
    }

    async #getWishlistVariantUri() {
        try {
            const form = this.#node.closest('form');
            const data = new FormData(form);

            data.append(this.#node.name, '')

            const response = await fetch(form.action, { method: 'POST', body: data })

            return await response.text();
        } catch (error) {
            console.error(error)
        }
    }

    #redirectToWishlist(path) {
        location.href = path;
    }
}

export default WishlistVariantButton;
