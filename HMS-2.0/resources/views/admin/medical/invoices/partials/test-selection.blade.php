<div class="bg-white rounded-lg shadow-sm h-full">
    <!-- Header -->
    <div class="border-b">
        <div class="px-4 py-3">
            <h2 class="text-lg font-medium text-gray-800">Lab Tests</h2>
        </div>
    </div>

    <!-- Lab Tests Panel -->
    <div id="labTestsPanel" class="tab-panel">
        <div class="p-3 border-b">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <input type="text" id="testSearch" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Search tests...">
                </div>
                <div>
                    <select id="categoryFilter" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">All Categories</option>
                        @foreach($testCategories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="p-3 overflow-y-auto" style="max-height: calc(100vh - 300px);">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" id="testGrid">
                @foreach($labTests as $test)
                <div class="test-card bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow duration-200 flex flex-col h-full w-full" data-category="{{ $test->department }}">
                    <div class="p-3 flex-grow w-full">
                        <h3 class="text-sm font-medium text-gray-800 line-clamp-2 min-h-[2rem] test-name" title="{{ $test->test_name }}">
                            {{ $test->test_name }}
                        </h3>

                        <div class="space-y-2 mt-2">
                            <div class="flex justify-between items-center">
                                <p class="text-xs text-gray-600 truncate max-w-[60%]">{{ $test->test_code }}</p>
                                <p class="text-xs font-medium text-blue-600">{{ $test->department }}</p>
                            </div>

                            <div class="flex justify-between items-center">
                                <p class="text-sm font-bold text-green-600">৳{{ number_format($test->price, 2) }}</p>
                                @if($test->duration_minutes)
                                <p class="text-xs text-gray-500">{{ $test->duration_minutes }}min</p>
                                @endif
                            </div>

                            @if($test->sample_type)
                            <div class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                {{ $test->sample_type }}
                            </div>
                            @endif

                            <input type="number"
                                class="quantity-input w-full text-sm border rounded py-1 px-2"
                                placeholder="Quantity"
                                min="1"
                                value="1">
                        </div>
                    </div>

                    <div class="p-3 pt-0 w-full">
                        <button class="add-test-btn w-full px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors duration-200"
                            data-test-id="{{ $test->id }}"
                            data-test-name="{{ $test->test_name }}"
                            data-test-code="{{ $test->test_code }}"
                            data-price="{{ $test->price }}"
                            data-category="{{ $test->department }}"
                            data-sample-type="{{ $test->sample_type }}"
                            data-duration="{{ $test->duration_minutes }}">
                            <i class="fas fa-plus mr-1"></i>Add Test
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lab Tests functionality
        const testGrid = document.getElementById('testGrid');
        const testSearch = document.getElementById('testSearch');
        const categoryFilter = document.getElementById('categoryFilter');

        function filterTests() {
            const searchTerm = testSearch.value.toLowerCase();
            const selectedCategory = categoryFilter.value;
            const testCards = document.querySelectorAll('.test-card');

            testCards.forEach(card => {
                const testName = card.querySelector('.test-name').textContent.toLowerCase();
                const category = card.getAttribute('data-category');

                const matchesSearch = testName.includes(searchTerm);
                const matchesCategory = !selectedCategory || category === selectedCategory;

                card.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
            });
        }

        if (testSearch) {
            testSearch.addEventListener('input', filterTests);
            testSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const firstVisibleCard = document.querySelector('.test-card:not([style*="display: none"])');
                    if (firstVisibleCard) {
                        const addButton = firstVisibleCard.querySelector('.add-test-btn');
                        if (addButton) {
                            addButton.click();
                            testSearch.value = '';
                            filterTests();
                        }
                    }
                }
            });
        }

        if (categoryFilter) {
            categoryFilter.addEventListener('change', filterTests);
        }

        // Test card click handler
        if (testGrid && !testGrid.hasEventListener) {
            testGrid.hasEventListener = true;

            testGrid.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-test-btn') || e.target.closest('.add-test-btn')) {
                    const button = e.target.classList.contains('add-test-btn') ? e.target : e.target.closest('.add-test-btn');
                    const testCard = button.closest('.test-card');
                    const quantityInput = testCard.querySelector('.quantity-input');

                    const quantity = parseInt(quantityInput.value) || 1;
                    if (quantity <= 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid Quantity',
                            text: 'Please enter a valid quantity'
                        });
                        return;
                    }

                    const testData = {
                        testId: button.dataset.testId,
                        testName: button.dataset.testName,
                        testCode: button.dataset.testCode,
                        price: parseFloat(button.dataset.price),
                        category: button.dataset.category,
                        sampleType: button.dataset.sampleType,
                        duration: button.dataset.duration,
                        quantity: quantity
                    };

                    console.log('Test data prepared:', testData);

                    if (typeof window.addTestLine === 'function') {
                        window.addTestLine(testData);
                    } else {
                        console.error('addTestLine function not available');
                    }

                    // Clear quantity input
                    quantityInput.value = '1';
                }
            });
        }
    });
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .test-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .add-test-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    .add-test-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .grid-cols-2 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .sm\:grid-cols-2 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .lg\:grid-cols-3 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .lg\:grid-cols-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* Smooth transitions */
    .test-card {
        transition: all 0.3s ease;
    }

    /* Input styling within cards */
    .test-card input {
        transition: border-color 0.3s ease;
    }

    .test-card input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px #3b82f6;
    }
</style>