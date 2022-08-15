package cpsc2150.extendedTicTacToe;

import java.util.ArrayList;

/**
 * The TicTacToe controller class will handle communication between our TicTacToeView and our Model (IGameBoard and BoardPosition)
 *
 * This is where you will write code
 *
 * You will need to include your BoardPosition class, the IGameBoard interface
 * and the implementations from previous homeworks
 * If your code was correct you will not need to make any changes to your IGameBoard classes
 */
public class TicTacToeController{
    //our current game that is being played
    private IGameBoard curGame;

    //The screen that provides our view
    private TicTacToeView screen;


    public static final int MAX_PLAYERS = 10;
    private char[] playerOptions = {'X','O','A','M','T','L','V','P','Z','S'};
    private int np;
    private int option = 0;
    private boolean newGame = false;


    /**
     *
     * @param model the board implementation
     * @param view the screen that is shown
     * @param np The number of players for the game
     * @post the controller will respond to actions on the view using the model.
     */
    TicTacToeController(IGameBoard model, TicTacToeView view, int np){
        this.curGame = model;
        this.screen = view;
        this.np = np;
    }

    /**
     *
     * @param row the row of the activated button
     * @param col the column of the activated button
     * @pre row and col are in the bounds of the game represented by the view
     * @post The button pressed will show the right token and check if a player has won.
     */
    public void processButtonClick(int row, int col) {

        if(newGame) newGame();

        BoardPosition b = new BoardPosition(row, col);
        if(!curGame.checkSpace(b)){
            screen.setMessage("Marker is occupied, choose another position");
        }
        else {
            curGame.placeMarker(b, playerOptions[option]);
            screen.setMarker(row, col, playerOptions[option]);

            if (curGame.checkForWinner(b)) {
                screen.setMessage("Player " + playerOptions[option] + " wins! Click any button to play again");
                newGame = true;
            }
            else if (curGame.checkForDraw()) {
                screen.setMessage("It is a draw! Click any button to play again");
                newGame = true;
            }
            else {
                option++;
                if (option == np) option = 0;
                screen.setMessage("It is " + playerOptions[option] + "'s turn.");
            }
        }
    }

    private void newGame()
    {
        screen.dispose();
        GameSetupScreen screen = new GameSetupScreen();
        GameSetupController controller = new GameSetupController(screen);
        screen.registerObserver(controller);
    }
}
