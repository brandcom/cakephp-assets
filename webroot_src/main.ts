import "./scss/styles.scss";

uploadImports();

async function uploadImports()
{
    if (!document.querySelector('.js-assets-upload-wrapper')) {
        return;
    }

    const { default: initUploadField } = await import('./components/upload-field/initUploadField');
    initUploadField();
}
