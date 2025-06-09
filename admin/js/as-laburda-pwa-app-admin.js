/**
 * Admin-specific JavaScript for AS Laburda PWA App.
 * This file handles the React application for the admin dashboard.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/admin/js
 */

// Ensure React and ReactDOM are available globally (typically enqueued by WordPress or a build process)
// If not, you might need to include them via script tags in your plugin's enqueue_scripts.
// For this example, we assume they are accessible.
const { useState, useEffect } = React;
const { createRoot } = ReactDOM; // Using createRoot for React 18+

/**
 * Main Admin Dashboard Component.
 * This component acts as a router for different admin pages.
 */
const AdminDashboardApp = () => {
    // State to manage which admin page is currently active
    const [activePage, setActivePage] = useState('');
    const [loading, setLoading] = useState(true);
    const [message, setMessage] = useState('');

    useEffect(() => {
        // Determine the active page based on the URL query parameter 'page'
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get('page');

        if (page) {
            // Remove the plugin prefix to get a cleaner page name for routing
            const cleanPage = page.replace('as-laburda-pwa-app-', '');
            setActivePage(cleanPage);
        } else {
            // Default to 'dashboard' if no specific page is set
            setActivePage('dashboard');
        }
        setLoading(false); // Page determination is quick
    }, []);

    // Function to render content based on the active page
    const renderPageContent = () => {
        if (loading) {
            return <p>Loading admin page...</p>;
        }

        switch (activePage) {
            case 'dashboard':
                return <DashboardPage />;
            case 'app-builder':
                return <AppBuilderPage />;
            case 'business-listings':
                return <BusinessListingsPage />;
            case 'listing-plans':
                return <ListingPlansPage />;
            case 'app-plans':
                return <AppPlansPage />;
            case 'affiliates':
                return <AffiliatesPage />;
            case 'analytics':
                return <AnalyticsPage />;
            case 'ai-assistant':
                return <AIAssistantPage />;
            case 'settings':
                // Settings page content is already rendered by PHP, so React doesn't need to do much here
                return <p>Global settings are managed below.</p>;
            default:
                return <p>Page not found or not yet implemented: {activePage}</p>;
        }
    };

    return (
        <div className="aslp-admin-wrapper">
            {message && <div className="aslp-admin-message">{message}</div>}
            {renderPageContent()}
        </div>
    );
};

// --- Individual Page Components (Placeholders for now) ---

const DashboardPage = () => {
    const [apps, setApps] = useState([]);
    const [listings, setListings] = useState([]);
    const [loadingData, setLoadingData] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchDashboardData = async () => {
            try {
                // Fetch all apps
                const appsResponse = await jQuery.post(aslp_ajax_object.ajax_url, {
                    action: 'aslp_get_all_apps',
                    nonce: aslp_ajax_object.nonce // Using generic admin nonce
                });
                if (appsResponse.success) {
                    setApps(appsResponse.data.apps);
                } else {
                    setError(appsResponse.data.message || 'Failed to fetch apps.');
                }

                // Fetch all business listings
                const listingsResponse = await jQuery.post(aslp_ajax_object.ajax_url, {
                    action: 'aslp_get_all_business_listings',
                    nonce: aslp_ajax_object.nonce
                });
                if (listingsResponse.success) {
                    setListings(listingsResponse.data.listings);
                } else {
                    setError(listingsResponse.data.message || 'Failed to fetch listings.');
                }
            } catch (e) {
                setError('An error occurred while fetching dashboard data.');
                console.error('Dashboard data fetch error:', e);
            } finally {
                setLoadingData(false);
            }
        };

        fetchDashboardData();
    }, []);

    if (loadingData) {
        return <p>Loading dashboard data...</p>;
    }

    if (error) {
        return <p className="aslp-error-message">Error: {error}</p>;
    }

    return (
        <div className="aslp-dashboard-page">
            <h2>Dashboard Overview</h2>
            <div className="aslp-dashboard-stats">
                <div className="aslp-stat-card">
                    <h3>Total Apps</h3>
                    <p>{apps.length}</p>
                </div>
                <div className="aslp-stat-card">
                    <h3>Total Listings</h3>
                    <p>{listings.length}</p>
                </div>
                {/* Add more stats here */}
            </div>

            <h3>Recent Apps</h3>
            {apps.length > 0 ? (
                <ul className="aslp-list">
                    {apps.slice(0, 5).map(app => (
                        <li key={app.id}>
                            <strong>{app.app_name}</strong> (User: {app.user_id}) - {app.description.substring(0, 50)}...
                        </li>
                    ))}
                </ul>
            ) : (
                <p>No apps found.</p>
            )}

            <h3>Recent Business Listings</h3>
            {listings.length > 0 ? (
                <ul className="aslp-list">
                    {listings.slice(0, 5).map(listing => (
                        <li key={listing.id}>
                            <strong>{listing.listing_title}</strong> (Status: {listing.status}) - {listing.city}
                        </li>
                    ))}
                </ul>
            ) : (
                <p>No business listings found.</p>
            )}
        </div>
    );
};

const AppBuilderPage = () => {
    return (
        <div className="aslp-app-builder-page">
            <h2>App Builder Management</h2>
            <p>Manage app templates and user-created apps here.</p>
            {/* Future: Add components for managing app templates and user apps */}
        </div>
    );
};

const BusinessListingsPage = () => {
    return (
        <div className="aslp-business-listings-page">
            <h2>Business Listings Management</h2>
            <p>View and manage all business listings on your platform.</p>
            {/* Future: Add components for listing table, edit forms */}
        </div>
    );
};

const ListingPlansPage = () => {
    return (
        <div className="aslp-listing-plans-page">
            <h2>Listing Plans Management</h2>
            <p>Create and manage subscription plans for business listings.</p>
            {/* Future: Add components for plan creation/editing */}
        </div>
    );
};

const AppPlansPage = () => {
    return (
        <div className="aslp-app-plans-page">
            <h2>App Plans Management</h2>
            <p>Create and manage subscription plans for app creation.</p>
            {/* Future: Add components for app plan creation/editing */}
        </div>
    );
};

const AffiliatesPage = () => {
    return (
        <div className="aslp-affiliates-page">
            <h2>Affiliate Program Management</h2>
            <p>Manage affiliates, commissions, payouts, and creatives.</p>
            {/* Future: Add components for affiliate table, commission/payout management */}
        </div>
    );
};

const AnalyticsPage = () => {
    return (
        <div className="aslp-analytics-page">
            <h2>Website Analytics</h2>
            <p>View detailed statistics on app and listing performance.</p>
            {/* Future: Add components for charts and analytics data */}
        </div>
    );
};

const AIAssistantPage = () => {
    const [prompt, setPrompt] = useState('');
    const [aiResponse, setAiResponse] = useState('');
    const [loadingAi, setLoadingAi] = useState(false);
    const [errorAi, setErrorAi] = useState(null);

    const handleChat = async () => {
        if (!prompt.trim()) {
            setErrorAi('Please enter a prompt.');
            return;
        }
        setLoadingAi(true);
        setErrorAi(null);
        setAiResponse('');

        try {
            const response = await jQuery.post(aslp_ajax_object.ajax_url, {
                action: 'aslp_admin_ai_chat',
                nonce: aslp_ajax_object.nonce,
                prompt: prompt
            });

            if (response.success) {
                setAiResponse(response.data.response);
            } else {
                setErrorAi(response.data.message || 'Failed to get AI response.');
            }
        } catch (e) {
            setErrorAi('An error occurred during AI communication.');
            console.error('AI chat error:', e);
        } finally {
            setLoadingAi(false);
        }
    };

    return (
        <div className="aslp-ai-assistant-page">
            <h2>AI Assistant</h2>
            <p>Interact with the AI assistant to manage content, SEO, and more.</p>
            <div className="aslp-ai-chat-interface">
                <textarea
                    rows="5"
                    placeholder="Ask the AI a question or request content..."
                    value={prompt}
                    onChange={(e) => setPrompt(e.target.value)}
                    className="w-full p-2 border rounded-md"
                ></textarea>
                <button
                    onClick={handleChat}
                    disabled={loadingAi}
                    className="mt-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:opacity-50"
                >
                    {loadingAi ? 'Generating...' : 'Chat with AI'}
                </button>
                {errorAi && <p className="aslp-error-message text-red-500 mt-2">{errorAi}</p>}
                {aiResponse && (
                    <div className="aslp-ai-response mt-4 p-3 bg-gray-100 rounded-md border">
                        <strong>AI Response:</strong>
                        <p>{aiResponse}</p>
                    </div>
                )}
            </div>
            {/* Future: Add sections for SEO generation, content creation etc. */}
        </div>
    );
};

// Mount the React app to the appropriate div
document.addEventListener('DOMContentLoaded', function() {
    const dashboardRoot = document.getElementById('aslp-admin-dashboard-app');
    const appBuilderRoot = document.getElementById('aslp-app-builder-admin-app');
    const businessListingsRoot = document.getElementById('aslp-business-listings-admin-app');
    const listingPlansRoot = document.getElementById('aslp-listing-plans-admin-app');
    const appPlansRoot = document.getElementById('aslp-app-plans-admin-app');
    const affiliatesRoot = document.getElementById('aslp-affiliates-admin-app');
    const analyticsRoot = document.getElementById('aslp-analytics-admin-app');
    const aiAssistantRoot = document.getElementById('aslp-ai-assistant-admin-app');

    // Determine which root element is present on the current page and render the app there
    if (dashboardRoot) {
        createRoot(dashboardRoot).render(<AdminDashboardApp />);
    } else if (appBuilderRoot) {
        createRoot(appBuilderRoot).render(<AdminDashboardApp />);
    } else if (businessListingsRoot) {
        createRoot(businessListingsRoot).render(<AdminDashboardApp />);
    } else if (listingPlansRoot) {
        createRoot(listingPlansRoot).render(<AdminDashboardApp />);
    } else if (appPlansRoot) {
        createRoot(appPlansRoot).render(<AdminDashboardApp />);
    } else if (affiliatesRoot) {
        createRoot(affiliatesRoot).render(<AdminDashboardApp />);
    } else if (analyticsRoot) {
        createRoot(analyticsRoot).render(<AdminDashboardApp />);
    } else if (aiAssistantRoot) {
        createRoot(aiAssistantRoot).render(<AdminDashboardApp />);
    }
});
