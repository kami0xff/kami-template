export const sleep = (m: number): Promise<void> => new Promise((r) => setTimeout(r, m));

export const getCSRFToken = (): string => {
    return document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
}

export const debounce = (func: (...args: any[]) => void, wait: number): (...args: any[]) => void => {
    let timeout: ReturnType<typeof setTimeout> | undefined;
    return (...args: any[]) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    }
}

//BUTTON HELPERS MAYBE should be in a button class ?
export const disableButton = (button: HTMLButtonElement): void => {
    button.disabled = true;
    button.classList.add('disabled');
}

export const enableButton = (button: HTMLButtonElement): void => {
    button.disabled = false;
    button.classList.remove('disabled');
}

export const toggleButton = (button: HTMLButtonElement): void => {
    button.disabled = !button.disabled;
    button.classList.toggle('disabled');
}

export const loaderButton = (button: HTMLButtonElement): void => {
    button.disabled = true;
    button.classList.add('loading');
}

export const stopLoaderButton = (button: HTMLButtonElement): void => {
    button.disabled = false;
    button.classList.remove('loading');
}