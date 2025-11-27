// backend/index.js
const express = require('express');
const mysql = require('mysql2/promise');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const cors = require('cors');
require('dotenv').config();

const app = express();
app.use(cors());
app.use(express.json());

const JWT_SECRET = 'proxy-secret-2025';

// MySQL Connection
const pool = mysql.createPool({
  host: 'localhost',
  user: 'root',
  password: '', // your password
  database: 'proxyapp',
  port: 3306,
});

// === AUTH MIDDLEWARE ===
const auth = async (req, res, next) => {
  const token = req.headers.authorization?.split(' ')[1];
  if (!token) return res.status(401).json({ error: 'No token' });
  try {
    req.user = jwt.verify(token, JWT_SECRET);
    next();
  } catch (e) {
    res.status(401).json({ error: 'Invalid token' });
  }
};

// === ROUTES ===

// Signup
app.post('/api/signup', async (req, res) => {
  const { name, email, password } = req.body;
  if (!name || !email || !password) return res.status(400).json({ error: 'All fields required' });

  const hashed = await bcrypt.hash(password, 10);
  try {
    const [result] = await pool.query(
      'INSERT INTO User (name, email, password, role) VALUES (?, ?, ?, ?)',
      [name, email, hashed, 'user']
    );
    const token = jwt.sign({ id: result.insertId, role: 'user' }, JWT_SECRET, { expiresIn: '7d' });
    res.json({ token, user: { id: result.insertId, name, email, role: 'user' } });
  } catch (e) {
    res.status(400).json({ error: 'Email already exists' });
  }
});

// Login
app.post('/api/login', async (req, res) => {
  const { email, password } = req.body;
  if (!email || !password) return res.status(400).json({ error: 'Email & password required' });

  const [rows] = await pool.query('SELECT * FROM User WHERE email = ?', [email]);
  const user = rows[0];
  if (!user || !await bcrypt.compare(password, user.password)) {
    return res.status(401).json({ error: 'Invalid credentials' });
  }

  const token = jwt.sign({ id: user.id, role: user.role }, JWT_SECRET, { expiresIn: '7d' });
  res.json({ token, user: { id: user.id, name: user.name, email: user.email, role: user.role } });
});

app.get('/api/me', auth, async (req, res) => {
  const [rows] = await pool.query('SELECT id, name, email, role FROM User WHERE id = ?', [req.user.id]);
  res.json(rows[0]);
});

// Create Order
app.post('/api/orders', auth, async (req, res) => {
  const { proxyType, country, count, period, totalPrice } = req.body;
  await pool.query(
    'INSERT INTO `Order` (userId, proxyType, country, count, period, totalPrice, status) VALUES (?, ?, ?, ?, ?, ?, ?)',
    [req.user.id, proxyType, country, count, period, totalPrice, 'pending']
  );
  res.json({ success: true });
});

// Get User Orders
app.get('/api/orders', auth, async (req, res) => {
  const [rows] = await pool.query(
    'SELECT * FROM `Order` WHERE userId = ? ORDER BY createdAt DESC',
    [req.user.id]
  );
  res.json(rows);
});

// Admin: Get All Orders
app.get('/api/admin/orders', auth, async (req, res) => {
  if (req.user.role !== 'admin') return res.status(403).json({ error: 'Forbidden' });
  const [rows] = await pool.query(`
    SELECT o.*, u.name as userName, u.email as userEmail 
    FROM \`Order\` o 
    JOIN User u ON o.userId = u.id 
    ORDER BY o.createdAt DESC
  `);
  res.json(rows);
});

// Update Status
app.patch('/api/admin/orders/:id/status', auth, async (req, res) => {
  if (req.user.role !== 'admin') return res.status(403).json({ error: 'Forbidden' });
  const { status } = req.body;
  await pool.query('UPDATE `Order` SET status = ? WHERE id = ?', [status, req.params.id]);
  res.json({ success: true });
});

// Update Notes
app.patch('/api/admin/orders/:id/notes', auth, async (req, res) => {
  if (req.user.role !== 'admin') return res.status(403).json({ error: 'Forbidden' });
  const { notes } = req.body;
  await pool.query('UPDATE `Order` SET notes = ? WHERE id = ?', [notes, req.params.id]);
  res.json({ success: true });
});

const PORT = 5000;
app.listen(PORT, () => {
  console.log(`Backend LIVE at http://localhost:${PORT}`);
});