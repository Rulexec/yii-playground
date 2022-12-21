import {onDomInteractive} from './util/on-dom-interactive.js';
import {addUriParams} from './util/uri-params.js';
import {findParentNode} from './util/dom-traverse.js';

// https://www.npmjs.com/package/autocompleter
const Autocompleter = window.autocomplete;

class AutoComplete {
  constructor({element, hiddenInputElement, selectedItemsDivEl, endpoint}) {
    this.element = element;
    this.hiddenInputElement = hiddenInputElement;
    this.selectedItemsDivEl = selectedItemsDivEl;
    this.endpoint = endpoint;

    this.selectedIds = new Set();
  }

  init({selectedItemsObj}) {
    if (selectedItemsObj) {
      selectedItemsObj.forEach(({id, title}) => {
        this._addItem({label: title, value: id});
      });
    }

    Autocompleter({
      input: this.element,
      minLength: 1,
      debounceWaitMs: 150,
      preventSubmit: true,
      fetch: (text, update) => {
        (async () => {
          const response = await fetch(
            addUriParams(this.endpoint, {pattern: text}),
          );

          if (response.status !== 200) {
            throw new Error(`Status !== 200, status: ${response.status}`);
          }

          const data = await response.json();
          const {items} = data;

          const mappedItems = [];

          items.forEach(({id, title}) => {
            id = String(id);

            if (this.selectedIds.has(id)) {
              // Skip already selected itemss
              return;
            }

            mappedItems.push({label: title, value: id});
          });

          update(mappedItems);
        })().catch((error) => {
          console.error(error);
        });
      },
      onSelect: (item) => {
        this.element.value = '';

        this._addItem(item);
      },
    });
  }

  _addItem(item) {
    const {label, value: id} = item;

    if (this.selectedIds.has(id)) {
      return;
    }

    this.selectedIds.add(id);

    this._updateHiddenInput();

    const labelEl = document.createElement('label');
    const titleEl = document.createElement('span');
    const removeEl = document.createElement('button');

    labelEl.appendChild(titleEl);
    labelEl.appendChild(document.createTextNode(' '));
    labelEl.appendChild(removeEl);

    titleEl.textContent = label;
    removeEl.textContent = '[x]';
    removeEl.setAttribute('type', 'button');

    removeEl.addEventListener('click', () => {
      if (!this.selectedIds.has(id)) {
        return;
      }

      this.selectedIds.delete(id);
      this.selectedItemsDivEl.removeChild(labelEl);

      this._updateHiddenInput();
    });

    this.selectedItemsDivEl.appendChild(labelEl);
  }

  _updateHiddenInput() {
    const value = Array.from(this.selectedIds).join(',');

    this.hiddenInputElement.value = value;
  }
}

onDomInteractive(() => {
  const elements = document.querySelectorAll(
    '[data-autocomplete-endpoint]:not([data-autocomplete-model-handled])',
  );

  for (const el of elements) {
    el.setAttribute('data-autocomplete-model-handled', '');

    const hiddenInputElementName = el.getAttribute(
      'data-autocomplete-multiselect-input-name',
    );
    const formEl = findParentNode(
      el,
      (maybeFormEl) => maybeFormEl.tagName === 'FORM',
    );
    const selectedItemsDivClassName = el.getAttribute(
      'data-autocomplete-multiselect-selected-class',
    );
    const selectedItemsDivEl = formEl.querySelector(
      `.${selectedItemsDivClassName}`,
    );

    const hiddenInputEl = formEl.querySelector(
      `[name="${hiddenInputElementName}"]`,
    );

    const endpoint = el.getAttribute('data-autocomplete-endpoint');

    const selectedItemsObjName = el.getAttribute('data-autocomplete-obj');
    const selectedItemsObj = window[selectedItemsObjName];

    const autocomplete = new AutoComplete({
      element: el,
      hiddenInputElement: hiddenInputEl,
      selectedItemsDivEl,
      endpoint,
    });
    autocomplete.init({selectedItemsObj});
  }
});
