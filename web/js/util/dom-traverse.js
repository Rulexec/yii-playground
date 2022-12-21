export function findParentNode(element, fun) {
    let el = element.parentNode;
    while (el) {
        if (fun(el)) {
            return el;
        }

        el = el.parentNode;
    }

    return null;
}