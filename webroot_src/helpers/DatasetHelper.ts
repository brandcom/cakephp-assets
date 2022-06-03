export class DatasetHelper {

    private element: HTMLElement;

    constructor(el: string|Element)
    {
        if (el instanceof Element) {
            this.element = el as HTMLElement;
            return;
        }

        let selector = el as string;
        let element: HTMLElement|null = document.querySelector(selector);
        if (element !== null) {
            this.element = element;
            return;
        }

        this.element = new HTMLElement();
        console.error('No HTMLElement found for selector ' + selector);
    }

    get data() : {[key: string]: string|undefined}
    {
        const dataset = this.dataSet;
        const htmlData = this.htmlData;
        const jsonData = this.jsonData;

        htmlData.forEach((item: {html: string, title: string}) => {
            dataset[item.title] = item.html;
        });

        jsonData.forEach((item: {title: string, json: string}) => {
            try {
                dataset[item.title] = JSON.parse(item.json);
            } catch (e) {
                console.error('JSON string for "' + item.title + '" could not be converted to an object. In php, use flags "JSON_HEX_QUOT | JSON_HEX_TAG" for json_encode()')
                console.error(e);
            }
        });

        return dataset;
    }

    /**
     * E.g.
     * <div class="vue-data"
     *      data-example="This is an example"
     *      data-test="This is another example"
     * ></div>
     */
    get dataSet(): {[name: string]: string|undefined}
    {
        const vueData = this.element.querySelector('.vue-data');

        return {...(vueData as HTMLElement)?.dataset} || {};
    }

    /**
     * E.g.
     * <template class="vue-html" title="anotherExample">
     *     <h1>This is another example</h1>
     *     <p>
     *         This html will end up as an html string in the prop "anotherExample".
     *     </p>
     * </template>
     */
    get htmlData(): { title: string, html: string }[]
    {
        const vueHtml = Array.from(this.element.querySelectorAll('.vue-html'));

        return vueHtml.map(el => {
            const element = el as HTMLElement;
            return { title: element.title, html: element.innerHTML };
        });
    }

    /**
     * E.g.
     * <div class="hidden vue-json" title="orders">
     *     <?= json_encode($orders, JSON_HEX_QUOT | JSON_HEX_TAG) ?>
     * </div>
     */
    get jsonData(): { title: string, json: string }[]
    {
        const vueJSON = Array.from(this.element.querySelectorAll('.vue-json'));

        return vueJSON.map(el => {
            const element = el as HTMLElement;
            return { title: element.title, json: element.innerText };
        });
    }
}
