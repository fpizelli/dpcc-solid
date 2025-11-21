## dpcc-solid

Este repositório abriga o projeto de controle de estacionamento desenvolvido para a disciplina de Design Patterns e Clean Code do quarto semestre do curso de Análise e Desenvolvimento de Sistemas. Trata-se de um sistema simplificado de estacionamento, desenvolvido em PHP puro.

---

### Integrantes do grupo

- Luís Felipe Pizelli Marques
- Luan Moreira Alves
- Leonado Ribeiro de Assis

---

### O que o sistema faz

- Registrar entrada de veículo;
- Registrar saída do veículo;
- Calcular valor a pagar pelo tempo parado;
- Mostrar relatório com quantidade e faturamento por tipo.

---

### Decisões de projeto

- **Camadas separadas**
  - `Domain` guarda entidades e interfaces, com `Vehicle`, `Car`, `Motorbike`, `Truck`, e a interface do repositório e da estratégia de preço.
  - `Application` tem os casos de uso. Cada caso de uso faz uma coisa só: registrar entrada, registrar saída, gerar relatório.
  - `Infra` abriga o banco SQLite e classes que calculam o preço.

- **Uso de interfaces**
  - `ParkingRepositoryInterface` define o que um repositório de estacionamento precisa fazer.
  - `PricingStrategyInterface` define como calcular preço por tipo de veículo.
  - Os casos de uso recebem só as interfaces (DIP básico), e a implementação concreta fica na camada Infra.

- **SQLite como banco**
  - Conexão é feita com PDO em `SQLiteConnection`.
  - A tabela `parking_records` é criada automaticamente se não existir.

- **HTML simples com Tailwind via CDN**
  - Interface é um arquivo `templates/index.html` com HTML direto.
  - Tailwind entra só por CDN para facilitar a parte visual, sem build.

- **Cálculo de preço simples**
  - Cada tipo de veículo tem uma classe de preço:
    - `CarPricing` - carro
    - `MotorbikePricing` - moto
    - `TruckPricing` - caminhão
  - Todas implementam `PricingStrategyInterface`.
  - O cálculo se dá pela diferença entre entrada e saída em segundos, convertida para horas (`ceil`), com o mínimo de 1 hora, e multiplica pela taxa.

- **Tipos de veículo padronizados**
    - `car` para carro
    - `bike` para moto
    - `truck` para caminhão
  - O formulário manda exatamente esses valores.

---

### Regras de negócio

- Preço por hora (arredondando para cima):
  - Carro (`car`): 5 por hora
  - Moto (`bike`): 3 por hora
  - Caminhão (`truck`): 10 por hora

- Entrada:
  - Usuário informa placa e tipo (car/bike/truck).
  - O sistema grava a data/hora atual no banco, sem calcular nada ainda.

- Saída:
  - Usuário informa placa.
  - Sistema procura o último registro dessa placa sem saída.
  - Calcula o preço e grava saída + valor.
  - Se não achar registro aberto, mostra mensagem avisando.

- Relatório:
  - Consulta o banco, agrupa por tipo.
  - Mostra quantos já saíram de cada tipo e quanto foi faturado.

---

### Como rodar o projeto

Pré-requisitos:

- PHP 8.2 ou maior
- Composer instalado

Passo a passo (na raiz do projeto):

1. Instalar dependências (se ainda não tiver `vendor/`):

   ```bash
   composer install
   ```

2. Garantir o autoload (por garantia):

   ```bash
   composer dump-autoload
   ```

3. Subir o servidor embutido do PHP apontando para `public/`:

   ```bash
   php -S localhost:8000 -t public
   ```

4. Abrir no navegador:

   ```text
   http://localhost:8000
   ```

Na primeira vez que rodar, o arquivo `database.sqlite` é criado na raiz. A tabela usada pelo sistema é `parking_records`.

---

### Estrutura do projeto

- `public/`
  - `index.php` - arquivo que recebe as requisições, chama os casos de uso e monta as mensagens/HTML.

- `templates/`
  - `index.html` - tela principal. Só imprime `<?= $message ?>` e `<?= $reportHtml ?>` e mostra os formulários.

- `src/`
  - `Domain/`
    - `Entities/`
      - `Vehicle.php` - classe base de veículo.
      - `Car.php`, `Motorbike.php`, `Truck.php` - tipos concretos.
    - `Repositories/`
      - `ParkingRepositoryInterface.php` - contrato do repositório.
    - `Services/`
      - `PricingStrategyInterface.php` - contrato para cálculo de preço.
  - `Application/`
    - `UseCases/`
      - `RegisterVehicleEntry.php` - registra entrada.
      - `RegisterVehicleExit.php` - registra saída e calcula valor.
      - `GenerateReport.php` - monta os dados do relatório.
  - `Infra/`
    - `Database/`
      - `SQLiteConnection.php` - cuida da conexão com SQLite e da criação da tabela.
      - `ParkingRepository.php` - implementação do repositório usando PDO.
    - `Services/`
      - `CarPricing.php`, `MotorbikePricing.php`, `TruckPricing.php` - implementações de preço por tipo.

- `vendor/`
  - gerado pelo Composer.

---

### Resumo do fluxo

1. Usuário abre `/`.
2. Preenche o formulário de entrada ou saída e envia.
3. `public/index.php` decide qual caso de uso chamar com base em `action`.
4. Caso de uso mexe no repositório e nas estratégias de preço.
5. Resultado volta para o `index.php`, que monta `$message` e `$reportHtml`.
6. Template `index.html` só mostra os dados prontos.

As mensagens usam `$_SESSION` para aparecer só depois do POST e não ficar repetindo a mesma mensagem em todo F5.
