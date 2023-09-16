import { BASE_URL } from "./constants"
export const checkAnswer = async (gameId, answer) => {
    try {

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ answer })
        }

        const response = await fetch(`${BASE_URL}api/check-game-answer/${gameId}`, requestOptions)
        const data = await response.json()
        return data
    } catch (err) {
        throw new Error(err.message)
    }
}

export const createGame = async (gameType) => {

    try {
        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ game_type: gameType })
        }

        const response = await fetch(`${BASE_URL}game-path`, requestOptions)
        const data = await response.json()
        return data
    } catch (err) {
        throw new Error(err.message)
    }

}


export const updateGame = async (gameId, params) => {
    try {

        const requestOptions = {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(params)
        }

        const response = await fetch(`${BASE_URL}api/games/${gameId}`, requestOptions)
        const data = await response.json()
        return data
    } catch (err) {
        throw new Error(err.message)
    }
}