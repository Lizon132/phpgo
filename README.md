# 15x15 Go Board Game in PHP

## Project Description
This PHP project implements a simple, browser-based 15x15 Go board game. The game tracks the players' scores and allows stones to be placed on the board. It includes features for checking liberties of stones, capturing groups, and resetting the game. This game is a great starting point for those interested in developing board games using web technologies.

## Features
- 15x15 Go board
- Score tracking for Black and White stones
- Stone placement with simple click interaction
- Automatic capture of stones with no liberties
- Board reset functionality

## Installation
1. Clone the repository to your local machine.
2. Ensure you have a PHP server set up (like XAMPP or WAMP).
3. Place the project in your server's root directory.
4. Open the project in your browser through the server (e.g., `http://localhost/your_project_folder`).

## Usage
- The game starts with an empty 15x15 board.
- Click on a cell to place a stone. The game alternates between black and white stones.
- The score is updated based on the number of captured stones.
- Use the "Reset Board" button to start a new game.

## Code Structure
- `index.php`: Main file containing HTML and PHP code for the game interface and logic.
- `style`: CSS styles for the board and stones.
- `script`: JavaScript function for handling stone placement.
- PHP session: Used to store the game state between requests.

## Contributing
Contributions are welcome! For major changes, please open an issue first to discuss what you would like to change.

## License
[MIT](https://choosealicense.com/licenses/mit/)

## Support
For support, email [support@example.com] or open an issue in the GitHub repository.
