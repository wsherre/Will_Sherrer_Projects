package cpsc2150.extendedTicTacToe;

/**
 * A class holding the row and column of a players piece
 * @invariant row >= 0
 * @invariant col >= 0
 */
public class BoardPosition {

    private int row;
    private int col;

    /**
     *
     * @param r row
     * @param c column
     *
     * @pre 0 <= row and 0 <= col
     * @post row = r and col = c
     */
    BoardPosition(int r, int c){
        row = r;
        col = c;
    }

    /**
     *
     * @return the row of the piece
     * @post 3 <= row <= 100
     */
    public int getRow(){
        return row;
    }

    /**
     *
     * @return the column of the piece
     * @post 3 <= col <= 100
     */
    public int getCol(){
        return col;
    }

    /**
     *
     * @param obj instance of the object class
     * @pre obj must be of type BoardPosition
     * @return true if the two BoardPositions have the same row and column, false otherwise
     * @post if equals returns true then it means that the Boardpositions are for the same game piece
     */
    @Override
    public boolean equals(Object obj) {
        if(!(obj instanceof BoardPosition))
            return false;

        BoardPosition b = (BoardPosition) obj;
        return this.getCol() == b.getCol() && this.getRow() == b.getRow();
    }

    /**
     *
     * @return a string containing the row and column of the boardposition
     * @post "[row],[column]"
     */
    @Override
    public String toString(){
        String s = "";

        return s += this.getRow() + "," + this.getCol();

    }
}
