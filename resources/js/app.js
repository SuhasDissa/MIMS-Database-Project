// Currency symbols mapping
const currencySymbols = {
    'USD': '$',
    'EUR': '€',
    'GBP': '£',
    'JPY': '¥',
    'AUD': 'A$',
    'CAD': 'C$',
    'CHF': 'Fr',
    'CNY': '¥',
    'INR': '₹',
    'SGD': 'S$',
};

// Format currency value
function formatCurrency(value) {
    if (!value) return '0.00';
    return parseFloat(value).toFixed(2);
}

// Function to fetch exchange rates from Sri Lankan Central Bank API
async function fetchExchangeRates() {
    try {
        const response = await fetch('https://venus.hnb.lk/api/get_rates_contents_web');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data.ex; // Return only the exchange rates array
    } catch (error) {
        console.error('Error fetching exchange rates:', error);
        return null;
    }
}

// Function to display exchange rates
function displayExchangeRates(rates) {
    if (!rates) return;
    
    const container = document.getElementById('exchange-rates');
    const template = document.getElementById('rate-card-template');
    
    if (!container || !template) return;
    
    // Clear existing rates
    container.innerHTML = '';

    // Sort rates by display order
    const sortedRates = [...rates].sort((a, b) => a.displayOrder - b.displayOrder);

    sortedRates.forEach(rate => {
        const clone = template.content.cloneNode(true);
        const currencyCode = rate.currencyCode;
        
        // Set currency symbol
        clone.querySelector('.currency-symbol').textContent = currencySymbols[currencyCode] || currencyCode;
        clone.querySelector('.currency-code').textContent = currencyCode;
        clone.querySelector('.buying-rate').textContent = formatCurrency(rate.buyingRate);
        clone.querySelector('.selling-rate').textContent = `Selling: ${formatCurrency(rate.sellingRate)}`;
        clone.querySelector('.currency-name').textContent = rate.currency;

        container.appendChild(clone);
    });

    // Update last update time
    const lastUpdateElement = document.getElementById('last-update-time');
    if (lastUpdateElement) {
        const lastUpdate = new Date(rates[0].updated_on);
        lastUpdateElement.textContent = lastUpdate.toLocaleString();
    }
}

// Initialize exchange rates when page loads
document.addEventListener('DOMContentLoaded', async () => {
    const rates = await fetchExchangeRates();
    if (rates) {
        displayExchangeRates(rates);
    }

    // Add refresh button functionality
    const refreshButton = document.getElementById('refresh-rates');
    if (refreshButton) {
        refreshButton.addEventListener('click', async () => {
            refreshButton.disabled = true;
            refreshButton.classList.add('loading');
            const newRates = await fetchExchangeRates();
            if (newRates) {
                displayExchangeRates(newRates);
            }
            refreshButton.disabled = false;
            refreshButton.classList.remove('loading');
        });
    }
});