<?php
session_start();

if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

$result = 'Output';
$error = '';
$expression = '';

function isValidExpression(string $expr): bool
{
    if (!preg_match('/^[0-9+\-*\/().,\s%^a-zA-Z_]+$/', $expr)) {
        return false;
    }

    preg_match_all('/[a-zA-Z_][a-zA-Z0-9_]*/', $expr, $matches);
    $allowed = ['sin', 'cos', 'tan', 'sqrt', 'log', 'exp', 'pow', 'abs', 'pi'];

    foreach ($matches[0] as $token) {
        if (!in_array(strtolower($token), $allowed, true)) {
            return false;
        }
    }

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'calculate';

    if ($action === 'clear_history') {
        $_SESSION['history'] = [];
        $result = 'History cleared';
    } else {
        $expression = trim($_POST['expression'] ?? '');

        if ($expression === '') {
            $error = 'Please enter an expression.';
        } elseif (!isValidExpression($expression)) {
            $error = 'Invalid expression. Use numbers, operators and supported functions only.';
        } else {
            $safeExpression = str_ireplace('pi', '(M_PI)', $expression);
            $safeExpression = str_replace('^', '**', $safeExpression);

            try {
                $value = null;
                eval('$value = ' . $safeExpression . ';');

                if (!is_numeric($value)) {
                    throw new Exception('Result is not numeric.');
                }

                $result = (string) $value;
                array_unshift($_SESSION['history'], [
                    'expression' => $expression,
                    'result' => $result,
                    'time' => date('H:i:s')
                ]);
                $_SESSION['history'] = array_slice($_SESSION['history'], 0, 10);
                $expression = $result;
            } catch (Throwable $th) {
                $error = 'Could not evaluate expression. Please check syntax.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exp 9 - Calculator</title>
    <link rel="stylesheet" href="style.css?v=3">
</head>

<body>
    <main class="container calculator-page">
        <h1>Scientific Calculator</h1>
        <p class="muted">Exp 9 - PHP + HTML + CSS</p>

        <section class="sum">
            <form method="post" id="calcForm">
                <input type="hidden" name="action" id="actionInput" value="calculate">
                <input type="text" id="expression" name="expression" value="<?php echo htmlspecialchars($expression); ?>" placeholder="0">

                <p class="result"><?php echo htmlspecialchars($result); ?></p>
                <?php if ($error !== ''): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>

                <div class="numbers">
                    <div class="numbers-row">
                        <button class="calc-btn scientific" type="button" onclick="press('sin(')">sin</button>
                        <button class="calc-btn scientific" type="button" onclick="press('cos(')">cos</button>
                        <button class="calc-btn scientific" type="button" onclick="press('tan(')">tan</button>
                        <button class="calc-btn scientific" type="button" onclick="press('sqrt(')">sqrt</button>
                    </div>

                    <div class="numbers-row">
                        <button class="calc-btn scientific" type="button" onclick="press('log(')">log</button>
                        <button class="calc-btn scientific" type="button" onclick="press('exp(')">exp</button>
                        <button class="calc-btn scientific" type="button" onclick="press('pow(')">pow</button>
                        <button class="calc-btn scientific" type="button" onclick="press('pi')">pi</button>
                    </div>

                    <div class="numbers-row">
                        <button class="calc-btn danger" type="button" onclick="clearExpression()">AC</button>
                        <button class="calc-btn" type="button" onclick="press('(')">(</button>
                        <button class="calc-btn" type="button" onclick="press(')')">)</button>
                        <button class="calc-btn operator" type="button" onclick="press('/')">/</button>
                    </div>

                    <div class="numbers-row">
                        <button class="calc-btn" type="button" onclick="press('7')">7</button>
                        <button class="calc-btn" type="button" onclick="press('8')">8</button>
                        <button class="calc-btn" type="button" onclick="press('9')">9</button>
                        <button class="calc-btn operator" type="button" onclick="press('*')">*</button>
                    </div>

                    <div class="numbers-row">
                        <button class="calc-btn" type="button" onclick="press('4')">4</button>
                        <button class="calc-btn" type="button" onclick="press('5')">5</button>
                        <button class="calc-btn" type="button" onclick="press('6')">6</button>
                        <button class="calc-btn operator" type="button" onclick="press('-')">-</button>
                    </div>

                    <div class="numbers-row">
                        <button class="calc-btn" type="button" onclick="press('1')">1</button>
                        <button class="calc-btn" type="button" onclick="press('2')">2</button>
                        <button class="calc-btn" type="button" onclick="press('3')">3</button>
                        <button class="calc-btn operator" type="button" onclick="press('+')">+</button>
                    </div>

                    <div class="numbers-row">
                        <button class="calc-btn" type="button" onclick="press('0')">0</button>
                        <button class="calc-btn" type="button" onclick="press('.')">.</button>
                        <button class="calc-btn" type="button" onclick="press('%')">%</button>
                        <button class="calc-btn equal" type="button" onclick="calculateNow()">=</button>
                    </div>

                    <div class="numbers-row">
                        <button class="calc-btn" type="button" onclick="press('^')">^</button>
                        <button class="calc-btn" type="button" onclick="backspace()">BS</button>
                        <button class="calc-btn history-clear" type="submit" onclick="document.getElementById('actionInput').value='clear_history'">Clear History</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>History (Last 10)</h2>
            <?php if (count($_SESSION['history']) === 0): ?>
                <p class="muted">No calculations yet.</p>
            <?php else: ?>
                <ul class="history-list">
                    <?php foreach ($_SESSION['history'] as $item): ?>
                        <li>
                            <span><?php echo htmlspecialchars($item['expression']); ?></span>
                            <strong>= <?php echo htmlspecialchars($item['result']); ?></strong>
                            <small><?php echo htmlspecialchars($item['time']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>

    <script>
        function press(value) {
            const input = document.getElementById('expression');
            input.value += value;
            input.focus();
        }

        function clearExpression() {
            document.getElementById('expression').value = '';
            document.getElementById('expression').focus();
        }

        function backspace() {
            const input = document.getElementById('expression');
            input.value = input.value.slice(0, -1);
            input.focus();
        }

        function calculateNow() {
            document.getElementById('actionInput').value = 'calculate';
            document.getElementById('calcForm').submit();
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                calculateNow();
            }
        });
    </script>
</body>

</html>
