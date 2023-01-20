import { DatasetHelper } from './DatasetHelper';
import { createApp } from 'vue';

export default class VueMountingHelper
{
    public static mount(vueAppMap: VueAppMap): void
    {
        vueAppMap.forEach(mapItem => {
            document.querySelectorAll(mapItem.cssSelector).forEach(el => {
                const data = new DatasetHelper(el).data;
                createApp(mapItem.vueApp, data).mount(el);
            });
        });
    }
}

export type VueAppMap = VueAppMapItem[];

export type VueAppMapItem = {
    cssSelector: string,
    vueApp: any,
};
