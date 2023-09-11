import { BASE_URL } from './constants.js'


document.getElementById('game-start-form').addEventListener('submit', async (event) => {

    event.preventDefault()
    const gameType = document.getElementById('game_type').value

    const response = await createGame(gameType)

})



const createGame = async (gameType) => {

    const requestOptions = {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ game_type: gameType })
    }

    const response = await fetch(`${BASE_URL}api/games`, requestOptions)
    const data = await response.json()
    console.log(data)
    return data

}