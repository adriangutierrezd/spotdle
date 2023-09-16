import { BASE_URL, PAUSE_BUTTON, PLAY_BUTTON, ARROW_NEXT_QUESITON } from './constants.js'
import { changeBtnState, isValidMp3Sound } from './utils.js'
import { checkAnswer, createGame, updateGame } from './gameService.js'
import Swal from '/node_modules/sweetalert2/src/sweetalert2.js'


document.getElementById('game-start-form').addEventListener('submit', async (event) => {

    event.preventDefault()

    const board = document.getElementById('game-board')
    document.getElementById('game-start-btn').dataset.previousInnerHtml = document.getElementById('game-start-btn').innerHTML
    changeBtnState(document.getElementById('game-start-btn'), 'loading')
    const gameType = document.getElementById('game_type').value

    let response;
    try {
        response = await createGame(gameType)
        if (response.status !== 201) throw new Error(response.message)
    } catch (err) {
        alert(err.message) // TODO -> Display error
    }

    changeBtnState(document.getElementById('game-start-btn'), 'restore')

    const gameId = response.data.gameId

    await getNextHint(gameId, 1)

})


const renderQuestion = (questionData, gameId) => {

    const gameBoard = document.getElementById('game-board')
    gameBoard.innerHTML = ''

    const questionForm = document.createElement('form')
    const questionLabel = document.createElement('label')
    questionLabel.className = 'question-label'
    questionLabel.innerText = `${questionData.text} -> ${questionData.value}`
    questionForm.appendChild(questionLabel)


    if (isValidMp3Sound(questionData.value)) {
        const song = new Audio(questionData.value)

        const playButton = document.createElement('a')
        playButton.href = '#'
        playButton.innerHTML = PLAY_BUTTON
        playButton.className = 'question-submit-button'
        playButton.addEventListener('click', () => {
            song.play()
        })
        playButton.href = '#'

        const pauseButton = document.createElement('a')
        pauseButton.href = '#'
        pauseButton.innerHTML = PAUSE_BUTTON
        pauseButton.className = 'question-submit-button'
        pauseButton.addEventListener('click', () => {
            song.pause()
        })

        questionForm.appendChild(playButton)
        questionForm.appendChild(pauseButton)



    }

    const questionInput = document.createElement('input')
    questionInput.type = 'text'
    questionInput.className = 'question-input-text'
    questionInput.id = 'question-input-field'
    questionInput.required = true
    questionForm.appendChild(questionInput)

    const formSubmitter = document.createElement('button')
    formSubmitter.type = 'submit'
    formSubmitter.innerHTML = ARROW_NEXT_QUESITON
    formSubmitter.className = 'question-submit-button'
    questionForm.appendChild(formSubmitter)

    questionForm.addEventListener('submit', async (submitEvent) => {

        submitEvent.preventDefault()

        const answer = document.getElementById('question-input-field').value

        await postGameAction(gameId, questionData.hint_order, questionData.hint_id, answer)
        await updateGame(gameId, { attempts: questionData.hint_order })

        const newHintNumber = ++questionData.hint_order

        const isRightAnswer = await checkAnswer(gameId, answer)
        if (isRightAnswer.message == 'Correct answer') {
            await updateGame(gameId, { state: 'COMPLETED', success: 1 })
            finishGame(gameId, true)
        } else {
            if (newHintNumber > 5) {
                await updateGame(gameId, { status: 'COMPLETED' })
                finishGame(gameId, false)
                return
            }
            getNextHint(gameId, newHintNumber)
        }
    })


    gameBoard.append(questionForm)
}

const postGameAction = async (gameId, attempt, hint, answer) => {

    try {

        const params = {
            game_id: gameId,
            attempt,
            hint,
            response: answer
        }

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(params)
        }

        const response = await fetch(`${BASE_URL}api/game-log`, requestOptions)
        const data = await response.json()
        return data
    } catch (err) {
        throw new Error(err.message)
    }

}

const getNextHint = async (gameId, hintNumber) => {
    let gamePath
    try {
        gamePath = await getGamePathNumber(gameId, hintNumber)
        if (gamePath.status !== 200) throw new Error(gamePath.message)
    } catch (error) {
        alert(error.message)
    }

    renderQuestion(gamePath.data, gameId)
}

const getGamePathNumber = async (gameId, hintOrder) => {

    try {
        const requestOptions = {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        }

        const response = await fetch(`${BASE_URL}api/game-path/${gameId}/${hintOrder}`, requestOptions)
        const data = await response.json()
        return data
    } catch (err) {
        throw new Error(err.message)
    }

}

const finishGame = (gameId, success) => {

    const swalOptions = {
        title: '¡Mala suerte!',
        text: `El/la artista que buscábamos era: `,
        imageUrl: 'https://unsplash.it/400/200',
        imageWidth: 400,
        imageHeight: 200,
        imageAlt: 'Custom image',
    }

    if (success) {
        swalOptions.title = '¡Genial!'
        fireSwal(swalOptions)
    } else {
        fireSwal(swalOptions)
    }

    // Quitamos opciones de la pantalla

}

const fireSwal = (options) => {
    Swal.fire(options)
}




