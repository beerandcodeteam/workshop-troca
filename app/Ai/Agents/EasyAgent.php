<?php

namespace App\Ai\Agents;

use App\Ai\Tools\AnalyzeBoardTool;
use App\Ai\Tools\ChooseFreeColorTool;
use App\Ai\Tools\EndTurnTool;
use App\Ai\Tools\ExecuteTradeTool;
use App\Ai\Tools\GetExcessTokenCountTool;
use App\Ai\Tools\PurchaseCardTool;
use App\Ai\Tools\ReturnTokensTool;
use App\Ai\Tools\RollDiceTool;
use App\Models\GameMatch;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::OpenAI)]
#[Model('gpt-5.4-nano')]
class EasyAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(private GameMatch $gameMatch) {}

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return view('prompts.easy-agent');
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new AnalyzeBoardTool($this->gameMatch),
            new RollDiceTool($this->gameMatch),
            new ChooseFreeColorTool($this->gameMatch),
            new EndTurnTool($this->gameMatch),
            new ExecuteTradeTool($this->gameMatch),
            new GetExcessTokenCountTool($this->gameMatch),
            new ReturnTokensTool($this->gameMatch),
            new PurchaseCardTool($this->gameMatch),
        ];
    }
}
