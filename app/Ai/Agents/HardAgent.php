<?php

namespace App\Ai\Agents;

use App\Ai\Tools\ChooseFreeColorTool;
use App\Ai\Tools\EndTurnTool;
use App\Ai\Tools\ExecuteTradeTool;
use App\Ai\Tools\GetExcessTokenCountTool;
use App\Ai\Tools\ImprovedAnalyzeBoardTool;
use App\Ai\Tools\PurchaseCardTool;
use App\Ai\Tools\ReturnTokensTool;
use App\Ai\Tools\RollDiceTool;
use App\Ai\Tools\SearchKnowledgeBaseTool;
use App\Models\GameMatch;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::OpenAI)]
#[Model('gpt-5.4-mini')]
class HardAgent implements Agent, Conversational, HasTools
{
    public function __construct(private GameMatch $gameMatch) {}

    use Promptable, RemembersConversations;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return view('prompts.hard-agent');
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new ImprovedAnalyzeBoardTool($this->gameMatch),
            new RollDiceTool($this->gameMatch),
            new ChooseFreeColorTool($this->gameMatch),
            new EndTurnTool($this->gameMatch),
            new ExecuteTradeTool($this->gameMatch),
            new GetExcessTokenCountTool($this->gameMatch),
            new ReturnTokensTool($this->gameMatch),
            new PurchaseCardTool($this->gameMatch),
            new SearchKnowledgeBaseTool,
        ];
    }
}
