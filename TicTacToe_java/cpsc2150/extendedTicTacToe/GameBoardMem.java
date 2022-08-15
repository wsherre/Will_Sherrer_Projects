package cpsc2150.extendedTicTacToe;


import java.util.*;

/**
 * This class holds information for the gameboard for tic-tac-toe in the form of a Hash Map
 *
 * @invariant    MIN_COL <= COL <= MAX_COL
 *               MIN_ROW <= ROW <= MAX_ROW
 *               MIN_TO_WIN <= NUMBER_TO_WIN <= MAX_TO_WIN and NUMBER_TO_WIN <= ROW and NUMBER_TO_WIN <= COL
 *              0 <= NumOfPieces <= ROWS * COLS
 *
 * @Correspondence game = Map< Character, ArrayList</BoardPosition>> ; the gameboard will be stored as a map that has
 *                       its keys be the player's character, each pointing to a list that will hold instances of BoardPosition
 *                       that correspond to a row and column that the piece is at
 *                 COL = number of columns the gameBoard has
 *                 ROW = number of rows the gameBoard has
 *                 NUM_TO_WIN = the amount of pieces needed to be in consecutive to win the game
 *                 NumOfPieces = number of total pieces currently on the gameboard
 */
public class GameBoardMem extends AbsGameBoard {

    private int ROW;
    private int COL;
    private int NUMBER_TO_WIN;
    private int NumOfPieces;
    private Map<Character, ArrayList<BoardPosition> > game;


    /**
     *
     * @param row maximum number of rows of the gameboard
     * @param col maximum number of columns in the gameboard
     * @param num the number in a row required to win
     * @post MAX_ROW = row and MAX_COL = col and NUMBER_TO_WIN = num and NumOfPieces = 0 and
     * [the game map will have a new key inserted into it one for each player's character. each key corresponds
     * to a list of type BoardPosition]
     */
    GameBoardMem(int row, int col, int num){
        ROW = row;
        COL = col;
        NUMBER_TO_WIN = num;
        NumOfPieces = 0;
        game = new HashMap<>();
    }


    public int getNumRows(){ return ROW; }

    public int getNumColumns(){ return COL; }

    public int getNumToWin(){ return NUMBER_TO_WIN; }

    @Override
    public boolean checkForDraw(){ return NumOfPieces == COL * ROW; }

    public void placeMarker(BoardPosition marker, char player){
        ArrayList<BoardPosition> l = new ArrayList<>();
        if(game.containsKey(player)) {
            l  = game.get(player);
            l.add(marker);
            game.put(player, l);
        }
        else{
            l.add(marker);
            game.put(player, l);
        }
        NumOfPieces++;
    }

    public char whatsAtPos(BoardPosition pos){
        if(game.isEmpty()) return ' ';

        for(Map.Entry<Character, ArrayList<BoardPosition> > m: game.entrySet()){
            if(isPlayerAtPos(pos, m.getKey())) return m.getKey();
        }
        return ' ';
    }

    @Override
    public boolean isPlayerAtPos(BoardPosition pos, char player) {
        ArrayList<BoardPosition> l = game.get(player);
        return l.contains(pos);
    }
}
