package cpsc2150.extendedTicTacToe;

/**
 * This interface will be able to handle the specifications of the gameboard as well as determine if a game has been won
 *
 * @Defines: COL: Z - the  amount of columns in the gameBoard
 *          ROW: Z - the  amount of rows in the gameboard
 *          NUMBER_TO_WIN: Z - the number of pieces in a row required to win
 *
 * @Initialization Ensures: [The GameBoard will start empty] and [That MAX_ROW, MAX_COL, NUMBER_TO_WIN are all initialized]
 *
 *
 * @Constraints: MIN_COL <= COL <= MAX_COL
 *               MIN_ROW <= ROW <= MAX_ROW
 *               MIN_TO_WIN <= NUMBER_TO_WIN <= MAX_TO_WIN and NUMBER_TO_WIN <= ROW and NUMBER_TO_WIN <= COL
 */
public interface IGameBoard {

    int MIN_ROW = 3;
    int MIN_COL = 3;
    int MAX_ROW = 20;
    int MAX_COL = 20;
    int MIN_TO_WIN = 3;
    int MAX_TO_WIN = 20;
    int MIN_PLAYERS = 2;
    int MAX_PLAYERS = 10;

    /**
     *
     * @return the number of Rows in the board
     * @post getNumRows = [number of rows] and MIN_ROW <= getNumRows() <= MAX_ROW
     */
    int getNumRows();

    /**
     *
     * @return the number of columns in the board
     * @post getNumColumns = [number of columns] and MIN_COL <= getNumColumns() <= MAX_COL
     */
    int getNumColumns();

    /**
     *
     * @return the number of pieces in a row required to win the game
     * @post getNumToWin = [pieces in a row to win] and MIN_TO_WIN <= getNumToWin() <= MAX_TO_WIN
     */
    int getNumToWin();

    /**
     *
     * @param marker instance of BoardPosition class with user's row and column
     * @param player character of the player
     * @pre [player is of type character] and [marker is of type BoardPosition]
     * @post changes the spot on the 2D array to the character's piece
     */
    void placeMarker(BoardPosition marker, char player);

    /**
     *
     * @param pos instance of BoardPosition class with user's row and column
     * @pre [pos must be of type BoardPosition]
     * @return the character at the row and column of the position.
     * @post whatsAtPos = [a character] or ' '
     */
    char whatsAtPos(BoardPosition pos);

    /**
     *
     * @return true if all of the board positions are filled with pieces, false otherwise
     * @post if return true then all of the positions on the board are filled the game is a draw
     */
    default boolean checkForDraw(){
        int row = getNumRows();
        int col = getNumColumns();
        for(int i = 0; i < row; ++i){
            for(int j = 0; j < col; ++j){
                BoardPosition b = new BoardPosition(i,j);
                if(whatsAtPos(b) == ' ') return false;
            }
        }
        return true;
    }


    /**
     *
     * @param pos instance of BoardPosition class with user's row and column
     * @param player character of the user's piece
     * @pre [player is of type character] and [pos is of type BoardPosition]
     * @return true if the player matches the character at the row and col, false otherwise
     * @post if return true the player is at that position
     */
    default boolean isPlayerAtPos(BoardPosition pos, char player){
        char piece = whatsAtPos(pos);
        return piece == player;
    }

    /**
     *
     * @param lastPos instance of BoardPosition class
     * @pre pos must be of type BoardPosition
     * @return true if the space on the board is ' ', false otherwise
     * @post if return is true then the space is available on the board and a piece can be placed there
     */
    default boolean checkSpace(BoardPosition lastPos){
        int MAX_COL = getNumColumns();
        int MAX_ROW = getNumRows();

            if(lastPos.getCol() < 0 || lastPos.getCol() >= MAX_COL) return false;

            else if(lastPos.getRow() < 0 || lastPos.getRow() >= MAX_ROW) return false;

            else return whatsAtPos(lastPos) == ' ';
        }

    /**
     *
     * @param lastPos instance of BoardPosition class with user's row and column
     * @pre lastPos must be of type BoardPosition
     * @return true if one of the functions that checks for the winner returns true, false otherwise
     * @post if return is true then the player has won the game
     */
    default boolean checkForWinner(BoardPosition lastPos){
        char p = whatsAtPos(lastPos);

        if(checkHorizontalWin(lastPos,p)) return true;

        else if(checkVerticalWin(lastPos, p)) return true;

        else return checkDiagonalWin(lastPos, p);
    }

    /**
     *
     * @param lastPos instance of BoardPosition class with user's row and column
     * @param player character of the user's piece
     * @pre [player is of type character] and [lastPos is of type BoardPosition]
     * @return true if there are NUMBER_TO_WIN of the user's pieces in a row horizontally, false otherwise
     * @post if return true then player won in the horizontal position
     */
    default boolean checkHorizontalWin(BoardPosition lastPos, char player){
        int c = lastPos.getCol();
        int row = lastPos.getRow();
        int count = 0;
        int NUMBER_TO_WIN = getNumToWin();
        int COL = getNumColumns();

        BoardPosition b = new BoardPosition(row,c);
        while(c >= 0 && whatsAtPos(b) == player){
            count++;
            c--;
            b = new BoardPosition(row,c);
        }
        c = lastPos.getCol() + 1;

        b = new BoardPosition(row,c);
        while(c < COL && whatsAtPos(b) == player){
            count++;
            c++;
            b = new BoardPosition(row,c);
        }

        return count >= NUMBER_TO_WIN;
    }

    /**
     *
     * @param lastPos instance of BoardPosition class with user's row and column
     * @param player character of the user's piece
     * @pre [player is of type character] and [lastPos is of type BoardPosition]
     * @return true if there are NUMBER_TO_WIN of the user's piece in a row vertically, false otherwise
     * @post if return true then player won in the vertical position
     */
    default boolean checkVerticalWin(BoardPosition lastPos, char player){
        final int col = lastPos.getCol();
        int r = lastPos.getRow();
        int NUMBER_TO_WIN = getNumToWin();
        int ROW = getNumRows();
        int count = 0;


        //start looping to the top of the board
        BoardPosition b = new BoardPosition(r,col);
        while(r >= 0 && whatsAtPos(b) == player){
            count++;
            r--;
            b = new BoardPosition(r,col);
        }
        r = lastPos.getRow() + 1;
        //start looping to the bottom of the board
        b = new BoardPosition(r,col);
        while(r < ROW && whatsAtPos(b) == player){
            count++;
            r++;
            b = new BoardPosition(r,col);
        }
        return count >= NUMBER_TO_WIN;
    }

    /**
     *
     * @param lastPos instance of BoardPosition class with user's row and column
     * @param player character of the user's piece
     * @pre [player is of type character] and [lastPos is of type BoardPosition]
     * @return true if there are NUMBER_TO_WIN of the user's piece in a row diagonally, false otherwise
     * @post if return true then player won in the diagonal position
     */
    default boolean checkDiagonalWin(BoardPosition lastPos, char player){
        int c = lastPos.getCol();
        int r = lastPos.getRow();
        int count = 0;
        int ROW = getNumRows();
        int COL = getNumColumns();
        int NUMBER_TO_WIN = getNumToWin();

        //start looping through the top of the '\' diagonal
        BoardPosition b = new BoardPosition(r,c);
        while(r >= 0 && c >= 0 && whatsAtPos(b) == player){
            count++;
            r--;
            c--;
            b = new BoardPosition(r,c);
        }
        c = lastPos.getCol() + 1;
        r = lastPos.getRow() + 1;
        //loop through the bottom of the '\' diagonal
        b = new BoardPosition(r,c);
        while(r < ROW && c < COL && whatsAtPos(b) == player){
            count++;
            r++;
            c++;
            b = new BoardPosition(r,c);
        }
        if(count >= NUMBER_TO_WIN) return true;

        count = 0;
        c = lastPos.getCol();
        r = lastPos.getRow();
        //start looping through the top of the '/' diagonal
        b = new BoardPosition(r,c);
        while(r >= 0 && c < COL && whatsAtPos(b) == player){
            count++;
            r--;
            c++;
            b = new BoardPosition(r,c);
        }
        c = lastPos.getCol() - 1;
        r = lastPos.getRow() + 1;
        //start looping through the bottom of the '/' diagonal
        b = new BoardPosition(r,c);
        while(r < ROW && c >= 0 && whatsAtPos(b) == player){
            count++;
            r++;
            c--;
            b = new BoardPosition(r,c);
        }
        return count >= NUMBER_TO_WIN;
    }
}
