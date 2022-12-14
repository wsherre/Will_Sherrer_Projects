package cpsc2150.extendedTicTacToe;

/**
 * Created by kplis on 4/5/2018.
 */
public class GameSetupController {
    private GameSetupScreen view;
    //private int max_size = 20;
    private final int MEM_CUTOFF = 64;


    public GameSetupController(GameSetupScreen v)
    {
        view = v;
    }

    public void processButtonClick(int rows, int cols, int players, int numWin )
    {
        String errorMsg = "";
        if(rows < IGameBoard.MIN_ROW || rows > IGameBoard.MAX_ROW)
        {
            errorMsg += "Rows must be between " +  IGameBoard.MIN_ROW + " and " + IGameBoard.MAX_ROW;
        }

        else if(cols < IGameBoard.MIN_COL || cols > IGameBoard.MAX_COL)
        {
            errorMsg += "Columns must be between " +  IGameBoard.MIN_COL + " and " + IGameBoard.MAX_COL;
        }

        else if (numWin > rows)
        {
            errorMsg += "Can't have more to win than the number of rows";
        }
        else if (numWin > cols)
         {
            errorMsg += "Can't have more to win than the number of Columns";

         }

        else if(numWin < IGameBoard.MIN_TO_WIN)
        {
            errorMsg += "Number to win must be at least " + IGameBoard.MIN_TO_WIN;
        }

        if(!errorMsg.equals(""))
        {
            view.displayError(errorMsg);

        }
        else
        {
            view.closeScreen();
            IGameBoard model;
            if(rows * cols <= MEM_CUTOFF) {
                model = new GameBoard(rows, cols, numWin);
            }
            else
            {
                model = new GameBoardMem(rows, cols, numWin);
            }
            TicTacToeView tview = new TicTacToeView(rows, cols);
            TicTacToeController tcontroller = new TicTacToeController(model, tview, players);

            tview.registerObserver(tcontroller);
        }
    }
}
