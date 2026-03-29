# TROCA - Jogo de Tabuleiro Digital contra IA

TROCA e a adaptacao digital de um jogo de tabuleiro brasileiro de troca de fichas. O jogador enfrenta um oponente controlado por IA em partidas estrategicas de gerenciamento de recursos. O objetivo e colecionar fichas coloridas (por dados ou trocas via cotacoes), comprar cartas e maximizar a pontuacao com base nas estrelas das cartas e fichas restantes.

**Tech stack:** PHP 8.5, Laravel 13, Livewire 4, Alpine.js, Tailwind CSS 4, PostgreSQL, Pest 4.

---

## Como Gerar o Projeto com os Prompts

Este projeto foi construido de forma iterativa usando o Claude Code. A pasta `prompts/` contem os prompts utilizados em sequencia. Abaixo esta o passo a passo para reproduzir o processo do zero.

### Passo 1: Criar a Descricao do Projeto

Use o prompt `prompts/prompt-para-criar-descricao-do-projeto.md` no Claude Code. Ele gera o arquivo `docs/project-description.md` com overview, tech stack e core workflows do projeto, baseado nas regras do jogo (`docs/game_rules.md`), nos designs (`docs/design/`) e nos materiais originais (`docs/jogo-original/`).

### Passo 2: Criar as User Stories

Use o prompt `prompts/prompt-para-user-stories.md`. Ele leva em conta a descricao do projeto gerada no passo anterior e cria historias de usuario detalhadas com criterios de aceitacao. O resultado e salvo em `docs/user-stories.md`.

### Passo 3: Criar o Schema do Banco de Dados

Use o prompt `prompts/prompt-para-database.md`. Ele gera o schema do banco em formato DBML baseado nas user stories e descricao do projeto, seguindo convencoes do Laravel. O resultado e salvo em `docs/database-schema.md`.

### Passo 4: Criar as Fases do Projeto

Use o prompt `prompts/prompt-para-phases.md`. Ele divide todo o projeto em fases numeradas com tarefas detalhadas e testes automatizados como criterio de aceitacao. O resultado e salvo em `docs/project-phases.md`.

### Passo 5: Gerar o Projeto com o ralph.sh

Com todos os documentos criados, execute o script `ralph.sh` para gerar o projeto automaticamente fase por fase:

```bash
./ralph.sh
```

O script le o `docs/project-phases.md`, separa em fases individuais e alimenta cada uma ao Claude Code (ou Codex) para implementacao automatizada, incluindo retries, commits automaticos e validacao de testes.
