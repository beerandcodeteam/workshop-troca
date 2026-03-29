# Devolver Fichas de Forma Estrategica

Quando voce ultrapassa o limite de 10 fichas, deve devolver o excesso antes de encerrar o turno. Quais fichas devolver faz diferenca significativa.

## Regra Principal

Devolva fichas de cores que NAO sao necessarias pela sua carta-alvo. Mantenha as cores que voce precisa para completar a proxima compra.

## Processo de Decisao

### 1. Identifique Sua Carta-Alvo
Qual carta face-up voce esta tentando comprar? Quais cores ela exige?

### 2. Classifique Suas Cores
- **Essenciais:** Cores que a carta-alvo exige e voce ainda nao tem o suficiente. NUNCA devolva essas.
- **Completas:** Cores que voce ja tem na quantidade exata necessaria. Evite devolver.
- **Excedentes:** Cores que voce tem alem do que a carta-alvo pede. Devolva o excedente.
- **Inuteis:** Cores que a carta-alvo nao exige (custo = 0 dessa cor). Devolva primeiro.

### 3. Prioridade de Devolucao
1. Primeiro: cores com custo 0 na carta-alvo (totalmente inuteis agora)
2. Segundo: cores com excedente sobre o necessario
3. Ultimo recurso: cores necessarias (apenas se nao tem outra opcao)

## Sem Carta-Alvo Definida

Se voce ainda nao tem um alvo claro:
- Analise todas as cartas face-up nos 4 compartimentos.
- Identifique quais cores aparecem mais nos custos dessas cartas.
- Devolva as cores MENOS representadas nos custos das cartas disponiveis — sao as menos uteis no futuro.

## Exemplo

Voce tem: 3 vermelhas, 3 verdes, 3 brancas, 2 amarelas = 11 fichas (1 excesso).
Carta-alvo exige: 2 vermelhas, 1 verde, 1 branca, 1 amarela, 0 azul.
Devolva: 1 verde OU 1 branca (ambas tem excedente sobre o necessario).
NAO devolva: amarela (tem exatamente o necessario) ou vermelha (precisa de 2, tem 3 mas pode usar 1 extra em outra carta futura — analise o contexto).
