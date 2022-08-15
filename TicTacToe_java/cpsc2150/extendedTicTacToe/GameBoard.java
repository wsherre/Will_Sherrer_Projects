package cpsc2150.extendedTicTacToe;

/**
 * The class that holds the information of the gameboard, can place and check markers and checks for a winner
 * @invariant MIN_COL <= COL <= MAX_COL
 *  *         MIN_ROW <= ROW <= MAX_ROW
 *  *         MIN_TO_WIN <= NUMBER_TO_WIN <= MAX_TO_WIN and NUMBER_TO_WIN <= ROW and NUMBER_TO_WIN <= COL
 *           0 <= NumOfPieces <= ROWS * COLS
 *
 * @Correspondence board = character array[0...ROWS][0...COLS]
 *                 COL = number of columns the gameBoard has
 *  *              ROW = number of rows the gameBoard has
 *  *              NUM_TO_WIN = the amount of pieces needed to be in consecutive to win the game
 *                 NumOfPieces = number of total pieces currently on the gameboard
 */
public class GameBoard extends AbsGameBoard{

    private int ROWS;
    private int COLS;
    private int NUMBER_TO_WIN;
    private int NumOfPieces;
    private char[][] board;

    /**
     *
     * @param row number of rows in the gameboard
     * @param col number of columns in the gameboard
     * @param num number of pieces in a row required to win
     * @pre MIN_ROW <= row <= MAX_ROW and MIN_COL <= col <= MAX_COL and MIN_TO_WIN <= num <= MAX_TO_WIN
     * @post ROWS = row and COLS = col and NUMBER_TO_WIN = num and NumOfPieces = 0 and [every character in board will be ' ']
     */
    GameBoard(int row, int col, int num){
        ROWS = row;
        COLS = col;
        NUMBER_TO_WIN = num;
        NumOfPieces = 0;
        board = new char[ROWS][COLS];

        for(int i = 0; i < ROWS; ++i){
            for(int j = 0; j < COLS; ++j){
                board[i][j] = ' ';
            }
        }
    }


    public int getNumRows(){
        return ROWS;
    }

    public int getNumColumns(){
        return COLS;
    }

    public int getNumToWin(){
        return NUMBER_TO_WIN;
    }

    @Override
    public boolean checkForDraw(){ return NumOfPieces == COLS * ROWS; }

    public void placeMarker(BoardPosition marker, char player){
        board[marker.getRow()][marker.getCol()] = player;
        NumOfPieces++;
    }

    public char whatsAtPos(BoardPosition pos){
        return board[pos.getRow()][pos.getCol()];
    }
}
