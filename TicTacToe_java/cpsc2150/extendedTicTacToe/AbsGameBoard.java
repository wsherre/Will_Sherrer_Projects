package cpsc2150.extendedTicTacToe;

public abstract class AbsGameBoard implements IGameBoard{


    /**
     *
     * @return a string of the gameboard
     * @post the string will have the pieces and spaces of the gameboard in a string that when printed will
     *      look similar to how an actual tic-tac-toe game board will look like
     */
    @Override
    public String toString(){
        int MAX_COL = getNumColumns();
        int MAX_ROW = getNumRows();

        String s = "  ";
        //if the row is greater than 9 then we will need to add a space to keep up with the 2 digit numbers
        if(MAX_ROW > 9) s+= " ";

        //adds the columns on the top line of the board to the string
        for(int i = 0; i < MAX_COL; ++i){
            if(i < 10)
                s += " ";
            s += i + "|";
        }
        s += "\n";

        //adds the rows to the string and the pieces
        for(int i = 0; i < MAX_ROW; ++i){
            //if the number of rows is 10 or more add a space in front of the single digits to keep the format correct
            if(i < 10 && MAX_ROW > 9)
                s += " ";
            s += i + "|";

            //adds the piece that is at the row and column, will either be X, O or ' '
            for(int j = 0; j < MAX_COL; ++j){
                BoardPosition b = new BoardPosition(i,j);
                s += whatsAtPos(b) + " |";
            }
            s += "\n";
        }

        return s;
    }
}
