# Dynamic Forms API - Complete Integration Guide

## 📋 Available Endpoints

### 1. **Web Form Routes** (Frontend Display)

#### **Display Form**
- **URL:** `GET /forms/{slug}`
- **Purpose:** Display the form on a full webpage
- **Response:** HTML page with form
- **Example:** `https://yourdomain.com/forms/student-registration`

#### **Submit Form**
- **URL:** `POST /forms/{slug}`
- **Purpose:** Submit form data via web interface
- **Response:** Redirect to success page or back with errors
- **Content-Type:** `application/x-www-form-urlencoded`

#### **Success Page**
- **URL:** `GET /forms/{slug}/success`
- **Purpose:** Display success message after form submission
- **Response:** HTML success page

---

### 2. **Embeddable Form Routes** (Iframe Integration)

#### **Display Embeddable Form**
- **URL:** `GET /embed/forms/{slug}`
- **Purpose:** Display form in iframe-friendly format
- **Response:** Compact HTML page optimized for embedding
- **Example:** `https://yourdomain.com/embed/forms/student-registration`

#### **Submit Embeddable Form**
- **URL:** `POST /embed/forms/{slug}`
- **Purpose:** Submit form data from embedded form
- **Response:** Redirect to embed success page
- **Content-Type:** `application/x-www-form-urlencoded`

#### **Embed Success Page**
- **URL:** `GET /embed/forms/{slug}/success`
- **Purpose:** Display success message in embedded context
- **Response:** Compact HTML success page

---

### 3. **API Routes** (JSON Integration)

#### **Get All Forms**
- **URL:** `GET /api/forms`
- **Purpose:** Get list of all active forms
- **Response:** JSON array of forms
- **Authentication:** None required

**Example Response:**
```json
{
  "success": true,
  "forms": [
    {
      "id": 1,
      "name": "Student Registration",
      "slug": "student-registration",
      "description": "Register for our programs"
    }
  ]
}
```

#### **Get Form Details**
- **URL:** `GET /api/forms/{slug}`
- **Purpose:** Get form structure and field definitions
- **Response:** JSON with form details and fields
- **Authentication:** None required

**Example Response:**
```json
{
  "success": true,
  "form": {
    "id": 1,
    "name": "Student Registration",
    "slug": "student-registration",
    "description": "Register for our programs",
    "captcha_enabled": true,
    "captcha_difficulty": "medium",
    "fields": [
      {
        "id": 1,
        "name": "full_name",
        "label": "Full Name",
        "type": "text",
        "required": true,
        "description": "Enter your full name",
        "options": null
      },
      {
        "id": 2,
        "name": "email",
        "label": "Email Address",
        "type": "email",
        "required": true,
        "description": "Your email address",
        "options": null
      },
      {
        "id": 3,
        "name": "gender",
        "label": "Gender",
        "type": "radio",
        "required": true,
        "description": "Select your gender",
        "options": [
          {"value": "male", "label": "Male"},
          {"value": "female", "label": "Female"},
          {"value": "other", "label": "Other"}
        ]
      }
    ]
  }
}
```

#### **Submit Form Data**
- **URL:** `POST /api/forms/{slug}/entries`
- **Purpose:** Submit form data via API
- **Content-Type:** `application/json`
- **Authentication:** None required
- **Response:** JSON success/error response

**Example Request:**
```json
{
  "full_name": "John Doe",
  "email": "john@example.com",
  "age": 25,
  "gender": "male",
  "interests": ["sports", "music"],
  "captcha_id": "cap_123456789",
  "captcha_answer": 15
}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Form submitted successfully",
  "entry_id": 42
}
```

#### **Get Captcha** (Legacy Route)
- **URL:** `POST /api/content/{slug}`
- **Purpose:** Legacy endpoint for backward compatibility
- **Same as:** `POST /api/forms/{slug}/entries`

---

### 4. **Captcha Routes**

#### **Get Captcha (API)**
- **URL:** `GET /api/captcha/{slug}`
- **Purpose:** Get captcha image and ID for API integration
- **Response:** JSON with captcha data
- **Authentication:** None required

**Example Response:**
```json
{
  "id": "cap_123456789",
  "image": "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAw...",
  "expires_at": "2025-07-08T12:30:00Z"
}
```

#### **Refresh Captcha (Web)**
- **URL:** `GET /captcha/refresh/{slug?}`
- **Purpose:** Refresh captcha for web forms
- **Response:** JSON with new captcha data
- **Authentication:** None required

---

## 🛠️ Integration Methods

### 1. **Direct Link Integration**

Simply link to the form URL:

```html
<a href="https://yourdomain.com/forms/student-registration" target="_blank">
  Fill Out Registration Form
</a>
```

### 2. **Iframe Integration**

Embed the form using iframe:

```html
<iframe 
  src="https://yourdomain.com/embed/forms/student-registration" 
  width="100%" 
  height="800" 
  frameborder="0"
  style="border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
</iframe>
```

### 3. **API Integration**

#### **JavaScript Example:**

```javascript
// Get form structure
async function getForm(slug) {
  const response = await fetch(`https://yourdomain.com/api/forms/${slug}`);
  return await response.json();
}

// Get captcha if needed
async function getCaptcha(slug) {
  const response = await fetch(`https://yourdomain.com/api/captcha/${slug}`);
  return await response.json();
}

// Submit form data
async function submitForm(slug, formData) {
  const response = await fetch(`https://yourdomain.com/api/forms/${slug}/entries`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify(formData)
  });
  
  return await response.json();
}

// Example usage
const form = await getForm('student-registration');
const captcha = await getCaptcha('student-registration');

const formData = {
  full_name: 'John Doe',
  email: 'john@example.com',
  age: 25,
  gender: 'male',
  interests: ['sports', 'music'],
  captcha_id: captcha.id,
  captcha_answer: 15
};

const result = await submitForm('student-registration', formData);
```

#### **PHP Example:**

```php
<?php
// Get form structure
function getForm($slug) {
    $url = "https://yourdomain.com/api/forms/{$slug}";
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Submit form data
function submitForm($slug, $formData) {
    $url = "https://yourdomain.com/api/forms/{$slug}/entries";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($formData)
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    return json_decode($response, true);
}

// Example usage
$form = getForm('student-registration');
$result = submitForm('student-registration', [
    'full_name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 25,
    'gender' => 'male',
    'interests' => ['sports', 'music']
]);
?>
```

#### **Python Example:**

```python
import requests
import json

def get_form(slug):
    url = f"https://yourdomain.com/api/forms/{slug}"
    response = requests.get(url)
    return response.json()

def submit_form(slug, form_data):
    url = f"https://yourdomain.com/api/forms/{slug}/entries"
    headers = {'Content-Type': 'application/json'}
    response = requests.post(url, json=form_data, headers=headers)
    return response.json()

# Example usage
form = get_form('student-registration')
result = submit_form('student-registration', {
    'full_name': 'John Doe',
    'email': 'john@example.com',
    'age': 25,
    'gender': 'male',
    'interests': ['sports', 'music']
})
```

### 4. **WordPress Integration**

Add this to your theme's `functions.php`:

```php
function dynamic_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'slug' => '',
        'type' => 'iframe', // 'iframe' or 'link'
        'width' => '100%',
        'height' => '800',
        'domain' => 'yourdomain.com',
        'text' => 'Fill Out Form'
    ), $atts);
    
    if (empty($atts['slug'])) {
        return '<p>Form slug is required</p>';
    }
    
    if ($atts['type'] === 'link') {
        return sprintf(
            '<a href="https://%s/forms/%s" target="_blank" class="btn btn-primary">%s</a>',
            $atts['domain'],
            $atts['slug'],
            $atts['text']
        );
    }
    
    return sprintf(
        '<iframe src="https://%s/embed/forms/%s" width="%s" height="%s" frameborder="0" style="border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></iframe>',
        $atts['domain'],
        $atts['slug'],
        $atts['width'],
        $atts['height']
    );
}
add_shortcode('dynamic_form', 'dynamic_form_shortcode');
```

Usage in WordPress:
```
[dynamic_form slug="student-registration"]
[dynamic_form slug="student-registration" type="link" text="Register Now"]
```

### 5. **React Component Example**

```jsx
import React, { useState, useEffect } from 'react';

const DynamicForm = ({ slug, domain = 'yourdomain.com' }) => {
    const [form, setForm] = useState(null);
    const [captcha, setCaptcha] = useState(null);
    const [formData, setFormData] = useState({});
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        loadForm();
    }, [slug]);

    const loadForm = async () => {
        try {
            const response = await fetch(`https://${domain}/api/forms/${slug}`);
            const data = await response.json();
            
            if (data.success) {
                setForm(data.form);
                
                // Load captcha if enabled
                if (data.form.captcha_enabled) {
                    const captchaResponse = await fetch(`https://${domain}/api/captcha/${slug}`);
                    const captchaData = await captchaResponse.json();
                    setCaptcha(captchaData);
                }
            }
        } catch (error) {
            console.error('Failed to load form:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);

        try {
            const submitData = { ...formData };
            
            if (captcha) {
                submitData.captcha_id = captcha.id;
                submitData.captcha_answer = formData.captcha_answer;
            }

            const response = await fetch(`https://${domain}/api/forms/${slug}/entries`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(submitData),
            });

            const result = await response.json();
            
            if (result.success) {
                alert('Form submitted successfully!');
                setFormData({});
            } else {
                alert('Error submitting form: ' + result.message);
            }
        } catch (error) {
            console.error('Submission error:', error);
            alert('Failed to submit form');
        } finally {
            setSubmitting(false);
        }
    };

    if (loading) return <div>Loading form...</div>;
    if (!form) return <div>Form not found</div>;

    return (
        <div className="dynamic-form">
            <h2>{form.name}</h2>
            {form.description && <p>{form.description}</p>}
            
            <form onSubmit={handleSubmit}>
                {form.fields.map((field) => (
                    <div key={field.id} className="form-field">
                        <label>{field.label} {field.required && '*'}</label>
                        {field.description && <p>{field.description}</p>}
                        
                        {field.type === 'text' && (
                            <textarea
                                value={formData[field.name] || ''}
                                onChange={(e) => setFormData({...formData, [field.name]: e.target.value})}
                                required={field.required}
                            />
                        )}
                        
                        {field.type === 'email' && (
                            <input
                                type="email"
                                value={formData[field.name] || ''}
                                onChange={(e) => setFormData({...formData, [field.name]: e.target.value})}
                                required={field.required}
                            />
                        )}
                        
                        {field.type === 'radio' && field.options && (
                            <div>
                                {field.options.map((option) => (
                                    <label key={option.value}>
                                        <input
                                            type="radio"
                                            name={field.name}
                                            value={option.value}
                                            checked={formData[field.name] === option.value}
                                            onChange={(e) => setFormData({...formData, [field.name]: e.target.value})}
                                        />
                                        {option.label}
                                    </label>
                                ))}
                            </div>
                        )}
                    </div>
                ))}
                
                {captcha && (
                    <div className="captcha-section">
                        <label>Security Verification *</label>
                        <img src={captcha.image} alt="Captcha" />
                        <input
                            type="number"
                            value={formData.captcha_answer || ''}
                            onChange={(e) => setFormData({...formData, captcha_answer: e.target.value})}
                            required
                        />
                    </div>
                )}
                
                <button type="submit" disabled={submitting}>
                    {submitting ? 'Submitting...' : 'Submit'}
                </button>
            </form>
        </div>
    );
};

export default DynamicForm;
```

---

## 🔧 Field Types and Validation

### **Supported Field Types:**

1. **text** - Multi-line text area
2. **email** - Email input with validation
3. **integer** - Number input
4. **date** - Date picker
5. **boolean** - Checkbox (true/false)
6. **radio** - Radio buttons (single select)
7. **checkbox** - Checkboxes (multi-select)
8. **select** - Dropdown menu

### **Field Properties:**

- `name` - Field identifier
- `label` - Display label
- `type` - Field type
- `required` - Whether field is required
- `description` - Help text
- `options` - Available options for radio/checkbox/select

### **Validation Rules:**

- Required fields are validated
- Email fields validate email format
- Integer fields accept numbers only
- Date fields validate date format
- Radio/checkbox/select validate against available options
- Captcha validation if enabled

---

## 🚀 Quick Start Examples

### **1. Simple Link Integration:**
```html
<a href="https://yourdomain.com/forms/contact-us">Contact Us</a>
```

### **2. Iframe Integration:**
```html
<iframe src="https://yourdomain.com/embed/forms/contact-us" width="100%" height="600"></iframe>
```

### **3. API Integration:**
```javascript
fetch('https://yourdomain.com/api/forms/contact-us/entries', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({name: 'John', email: 'john@example.com'})
})
```

All endpoints support CORS and are ready for cross-origin requests. The system automatically handles validation, captcha verification, and data storage.
