<?php

namespace App\Commands;

use App\Services\CardService;
use App\Services\GameService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Mockery\Exception;

class GameCommand extends Command
{

    /**
     * @var GameService
     */
    private $gameService;
    /**
     * @var CardService
     */
    private $cardService;


    /**
     * GameCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->gameService = new GameService();
        $this->cardService = new CardService();
    }

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'game:play';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'BlackjackGame';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment("Welcome Console BlackJack Game 😁");
        $playerName = null;
        $delay = null;
        try {
            $playerName = $this->askValid('Whats your name?', 'string');
            $delay = $this->askValid('How long should we wait after end', 'int');
        }catch (Exception $ex){
            $this->error('Wrong Input Please Check Your Inputs');
            exit();
        }
        while(true){
            $this->play($playerName);
            $this->comment("BlackJack Game Will Start After {$delay} seconds" );
            sleep($delay);
        }
    }

    protected function askValid($question, $type)
    {
        $value = $this->ask($question);
        if ($type == 'int'){
            if(!is_numeric($value)){
                throw new Exception('Unprocessable Entity');
            }
        }else if($type == 'string'){
            if(!is_string($value)){
                throw new Exception('Unprocessable Entity');
            }
        }

        return $value;
    }

    /**
     * @param $playerName
     * @return bool
     */
    public function play($playerName): bool
    {
        $choice = null;
        // initialize deck
        $deck = $this->cardService::prepareCards();

        $memberCardsArray = $this->gameService::initPlayerAndDealerCards($deck);
        $playerHand = $memberCardsArray[0];
        $dealerHand = $memberCardsArray[1];

        $this->info("Dealer Has: {$this->gameService::getHandDetailedArray($dealerHand)}");
        $this->info("Dealer Value: {$this->cardService::calculateHand($dealerHand)}");
        $this->info("{$playerName} Has: {$this->gameService::getHandDetailedArray($playerHand)}");
        $this->info("{$playerName} Value: {$this->cardService::calculateHand($playerHand)}");

        while ($choice != 'Stay') {

            if ($this->cardService::calculateHand($playerHand) > 21) {
                $this->error("Busted!");
                return false;
            }

            if ($this->cardService::calculateHand($dealerHand) == 21) {
                $this->comment("Dealer Won ! Hand: {$this->gameService::getHandDetailedArray($dealerHand)}" );
                return false;
            }

            elseif ($this->cardService::calculateHand($playerHand) == 21) {
                $this->comment("BlackJack! {$playerName} Win! Hand: {$this->gameService::getHandDetailedArray($playerHand)}");
                return true;
            }
            // Waiting Player Choice Stay Or Hit
            $choice = $this->waitChoice();
            switch ($choice) {

                //if choose hit pick card form deck and print last value
                case 'Hit':
                    $playerHand[] = array_pop($deck);
                    $this->info($this->gameService::getHandDetailedArray($playerHand));
                    $this->info("{$playerName} Value: {$this->cardService::calculateHand($playerHand)}");
                    break;
                default:
                    // do nothing
                    break;
            }
        }
        while (true) {
            if ($this->cardService::calculateHand($dealerHand) > 21) {
                $this->comment("Dealer Busted !");
                return true;
            } elseif ($this->cardService::calculateHand($dealerHand) == 21) {
                $this->comment("Dealer BlackJack ,  Dealer Won ! Hand: {$this->gameService::getHandDetailedArray($dealerHand)}");
                return false;
            } elseif ($this->cardService::calculateHand($dealerHand) < 17) {
                $dealerHand[] = array_pop($deck);
                $this->info("Dealer Hand :");
                $this->info($this->gameService::getHandDetailedArray($dealerHand));
                $this->info("Value : ". $this->cardService::calculateHand($dealerHand));
            } else {
                if ($this->cardService::calculateHand($dealerHand) >= $this->cardService::calculateHand($playerHand)) {
                    $this->comment("Dealer Win ! Hand: {$this->gameService::getHandDetailedArray($dealerHand)}");
                    return false;
                }
                else {
                    $this->comment("{$playerName} Win ! Hand: {$this->gameService::getHandDetailedArray($playerHand)}");
                    return true;
                }
            }

            sleep(1);
        }

    }

    //Player Hit Or Stay Choice
    public function waitChoice(){
        return $this->choice("Hit Or Stay", [
            'Hit',
            'Stay',
        ]);
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
