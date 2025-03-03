"use strict";
const mongoose = require('mongoose');
const FORMULAR_COLLECTION = 'issues';

const formularSchema = new mongoose.Schema({
    name: String,
    adresse: String,
    telefonnummer: String,
    email: String,
    newsletter: String
});

const formModel = mongoose.model(FORMULAR_COLLECTION, formularSchema);

module.exports = formModel;

