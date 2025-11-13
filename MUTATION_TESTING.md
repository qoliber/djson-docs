# Mutation Testing for DJson

## What is Mutation Testing?

**Mutation testing** is an advanced testing technique that measures the quality of your tests by introducing small bugs (mutations) into your code and checking if your tests catch them.

### How It Works:

1. **Infection PHP** modifies your source code (e.g., changes `>` to `>=`, removes function calls, changes return values)
2. Runs your test suite against each mutation
3. If a test **fails**, the mutation was "killed" ✅ (good - your tests caught the bug!)
4. If all tests **pass**, the mutation "escaped" ❌ (bad - your tests missed the bug!)

### Key Metrics:

- **MSI (Mutation Score Indicator)**: Percentage of mutations killed
  - **70%+ is good**, 80%+ is excellent, 90%+ is exceptional
- **Covered Code MSI**: MSI for code that has test coverage
- **Mutation Coverage**: More accurate than line coverage!

---

## Setup

### ✅ Already Done:

1. **Installed Infection PHP**:
   ```bash
   composer require --dev infection/infection
   ```

2. **Created Configuration** (`infection.json5`):
   - Configured to test `/src` directory
   - Set minimum MSI targets: 70% overall, 80% for covered code
   - Enabled all default mutators
   - Configured logging (HTML report, JSON, text)

---

## Running Mutation Tests

### Requirements:

You need ONE of these code coverage drivers:

#### Option 1: PCOV (Fastest - Recommended)
```bash
# Install pcov extension
sudo dnf install php-pecl-pcov   # Fedora/RHEL
# or
sudo apt install php-pcov         # Ubuntu/Debian

# Run infection
./vendor/bin/infection --threads=4
```

#### Option 2: Xdebug (Most Common)
```bash
# Install xdebug
sudo dnf install php-xdebug

# Configure xdebug mode
export XDEBUG_MODE=coverage

# Run infection
./vendor/bin/infection --threads=4
```

#### Option 3: phpdbg (Built-in, Slower)
```bash
# Use phpdbg (no installation needed)
phpdbg -qrr ./vendor/bin/infection --threads=4
```

---

## Interpreting Results

### Example Output:
```
Mutation Score Indicator (MSI): 85%
Covered Code MSI: 92%

100 mutations generated:
     85 killed
      8 escaped
      5 errors
      2 timeouts
```

### What Each Means:

- **Killed**: ✅ Test caught the mutation (good!)
- **Escaped**: ❌ Mutation survived - test gap found!
- **Errors**: Mutation caused fatal error (usually fine)
- **Timeouts**: Mutation caused infinite loop (usually fine)

---

## Example Mutations Infection Tests:

### 1. **Comparison Operators**
```php
// Original
if ($age >= 18) { ... }

// Mutation
if ($age > 18) { ... }  // Changes >= to >
```

### 2. **Return Values**
```php
// Original
return true;

// Mutation
return false;  // Flips boolean
```

### 3. **Arithmetic**
```php
// Original
$total = $price + $tax;

// Mutation
$total = $price - $tax;  // Changes + to -
```

### 4. **Array Operations**
```php
// Original
if (!empty($array)) { ... }

// Mutation
if (empty($array)) { ... }  // Removes negation
```

### 5. **Function Calls**
```php
// Original
$result = $this->processValue($value);

// Mutation
$result = $value;  // Removes function call
```

---

## What DJson's 100 Tests Would Catch:

With **100 tests and 379 assertions**, our mutation score should be excellent because we test:

✅ **All directives**: if, unless, exists, for, else, set, match/switch
✅ **All operators**: Comparison, logical (&&, ||, !), ternary
✅ **Edge cases**: Empty arrays, null values, missing keys
✅ **Nested structures**: Deep nesting, loops in conditionals
✅ **Functions**: 20+ built-in functions tested
✅ **Real-world scenarios**: E-commerce, permissions, shipping

### Expected MSI: **75-85%**

This is excellent for a templating engine!

---

## Viewing Reports

After running mutation tests:

### HTML Report (Best for Analysis):
```bash
open infection-report.html
# or
firefox infection-report.html
```

Shows:
- Which mutations escaped
- Exact code location
- Which mutator was used
- Suggestions for improvement

### Text Summary:
```bash
cat infection-summary.log
```

### Per-Mutator Stats:
```bash
cat infection-per-mutator.md
```

---

## Improving Mutation Score

If mutations escape, add tests for:

1. **Boundary conditions**: Test `>=` vs `>`, `<=` vs `<`
2. **Boolean flips**: Test both true/false paths
3. **Return values**: Verify exact return values, not just "truthy"
4. **Array operations**: Test empty, single item, multiple items
5. **Error conditions**: Test what happens with invalid input

---

## Commands Reference

```bash
# Run with default settings
./vendor/bin/infection

# Show escaped mutations
./vendor/bin/infection --show-mutations

# Run on multiple threads (faster)
./vendor/bin/infection --threads=4

# Test only specific files
./vendor/bin/infection --filter=src/DJson.php

# Dry run (see what would be tested)
./vendor/bin/infection --dry-run

# Set minimum MSI threshold
./vendor/bin/infection --min-msi=80

# Use existing coverage (faster)
./vendor/bin/phpunit --coverage-xml=build/coverage
./vendor/bin/infection --coverage=build/coverage
```

---

## CI/CD Integration

Add to your CI pipeline:

```yaml
# GitHub Actions example
- name: Run Mutation Tests
  run: |
    composer install
    vendor/bin/infection --threads=4 --min-msi=70 --logger-github
```

---

## Why Mutation Testing Matters

### Traditional Code Coverage:
```php
function divide($a, $b) {
    return $a / $b;  // 100% line coverage!
}

test('divides numbers', () => {
    assertEquals(5, divide(10, 2));
});
```
✅ **100% line coverage** but... what about division by zero?

### Mutation Testing Catches This:
```php
// Mutation: Changes / to *
return $a * $b;  // Test still passes! ❌ ESCAPED

// Mutation: Removes division
return $a;  // Test fails! ✅ KILLED
```

Mutation testing forces you to write **better, more comprehensive tests**!

---

## DJson-Specific Mutation Examples

### 1. Directive Matching
```php
// src/Directives/IfDirective.php
public function matches(string $key): bool
{
    return preg_match('/^@djson if\s+.+/', $key) === 1;
}

// Mutation: Changes === to !==
// If no test checks for non-matching keys, this escapes!
```

### 2. Expression Evaluation
```php
// src/DJson.php
if (str_contains($condition, '&&')) { ... }

// Mutation: Changes && to ||
// Need tests that verify AND vs OR logic!
```

### 3. Loop Variables
```php
'_first' => $index === array_key_first($items)

// Mutation: Changes === to !==
// Need tests that check _first is true for first item!
```

---

## Next Steps

1. **Install code coverage driver** (pcov recommended)
2. **Run mutation tests**: `./vendor/bin/infection --threads=4`
3. **Review HTML report** to find escaped mutations
4. **Add tests** for any gaps found
5. **Re-run** until MSI > 80%

---

## Resources

- [Infection PHP Documentation](https://infection.github.io/)
- [Mutation Testing Explained](https://en.wikipedia.org/wiki/Mutation_testing)
- [Why Mutation Testing?](https://www.youtube.com/watch?v=ba_86FlRiKg)

---

**Your friend was right - mutation testing is awesome!** 🚀

It's the best way to ensure your tests actually test what they claim to test!
