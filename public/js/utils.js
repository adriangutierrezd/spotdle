import { LOADER } from './constants.js'

export const changeBtnState = (btn, state) => {
    if (state === 'loading') {
        btn.innerHTML = LOADER
        btn.setAttribute('disabled', true)
    } else {
        btn.innerHTML = btn.dataset.previousInnerHtml
        btn.removeAttribute('disabled')
    }
}

export const isValidMp3Sound = (name) => {
    try {
        new URL("/", name);
        return name.includes('mp3')
    } catch (error) {
        return false
    }
}