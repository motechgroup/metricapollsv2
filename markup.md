# METRICA POLLS

# Enterprise Research Intelligence Platform

## Developer Guide

Version: 2.0

---

# IMPORTANT

This document is the single source of truth for the entire project.

Every generated feature must comply with this guide.

Do not ignore any requirement.

Never redesign architecture without explicit approval.

---

# PROJECT OBJECTIVE

Build an enterprise-grade Research Intelligence Platform similar to GeoPoll, Ipsos, Kantar, Qualtrics and SurveyMonkey.

The application must support:

* Research Management
* Survey Platform
* Client Portal
* Public Website
* Public Opinion Portal
* Research Marketplace
* Research Academy
* Field Data Collection
* Enterprise Reporting
* AI Assisted Analytics

The platform must be scalable enough to support millions of respondents across Africa.

---

# TECHNOLOGY STACK

## Backend

Laravel 12

PHP 8.4+

MySQL 8+

---

## Frontend

Blade Templates

Tailwind CSS 4

Livewire 3

Alpine.js

Heroicons

Chart.js

SortableJS

Trix Editor (or TipTap if richer editing is needed)

---

## Authentication

Laravel Sanctum

Email Verification

OTP Verification

Two Factor Authentication

Remember Devices

---

## Authorization

Spatie Laravel Permission

Role Based Access Control

Policy Based Authorization

Permission Middleware

---

## Database

MySQL

Use UUIDs where appropriate.

Never use Eloquent relationships inefficiently.

Always eager load relationships.

Index every searchable column.

Use soft deletes where applicable.

Use foreign key constraints.

---

## File Storage

Local Storage (development)

Shared Hosting Storage (production)

Cloud storage support must remain pluggable.

Never tightly couple the application to a single storage provider.

---

## Queue System

Laravel Queues

Database Queue by default.

Redis queue support when available.

Queue:

* Emails
* Notifications
* Report Generation
* Data Processing
* AI Processing
* Scheduled Jobs

---

## Cache

Laravel Cache

Preferred Driver:

Redis

Fallback:

Database Cache

Cache:

* Settings
* Roles
* Permissions
* Survey Metadata
* Statistics
* Dashboards
* Country Lists

Implement automatic cache invalidation.

---

## Security

Implement:

CSRF Protection

Rate Limiting

Content Security Policy

XSS Protection

SQL Injection Prevention

Request Validation

Audit Logs

Security Headers

Encrypted Sensitive Data

Session Rotation

Password Hashing

MFA Ready

---

## Rate Limiting

Authentication

Login:

5 attempts every 15 minutes

Registration:

3 per hour

OTP:

5 per hour

Password Reset:

3 per hour

API

100 requests per minute

Admin APIs

50 requests per minute

---

## Performance

Never perform N+1 queries.

Always paginate tables.

Lazy load large datasets.

Optimize queries.

Use eager loading.

Use indexes.

Cache expensive queries.

Queue long-running jobs.

---

# ARCHITECTURE

Use Modular Monolith Architecture.

Each module must be independent.

Each module contains:

Controllers

Models

Requests

Policies

Services

Repositories (only when necessary)

Events

Listeners

Observers

Notifications

Jobs

Routes

Views

Livewire Components

Tests

---

# DIRECTORY STRUCTURE

app/

Modules/

Authentication

Users

Roles

Permissions

Clients

Projects

SurveyBuilder

SurveyEngine

Responses

Reports

Analytics

PublicOpinion

Marketplace

Academy

FieldOperations

Payments

Wallet

CRM

HR

Settings

Notifications

AuditLogs

AI

Each module must be isolated.

Never place unrelated logic together.

---

# CODING STANDARDS

Strict typing.

Service Layer.

Form Request Validation.

Never place business logic inside controllers.

Never write raw SQL unless performance requires it.

Reusable components only.

Keep methods short.

Single Responsibility Principle.

SOLID principles.

PSR-12 coding standard.

---

# FRONTEND DESIGN

Corporate Design Only.

No gradients.

No glassmorphism.

No neon colors.

No gaming effects.

No crypto website styling.

Use:

White backgrounds

Professional typography

Generous spacing

Flat cards

Subtle shadows

Professional tables

Professional dashboards

Executive quality UI.

Design inspiration:

Kantar

Ipsos

McKinsey

Deloitte

PwC

Microsoft

---

# UI COMPONENTS

Build reusable components.

Buttons

Cards

Tables

Badges

Alerts

Dialogs

Drawers

Dropdowns

Forms

Inputs

Checkboxes

Radio Buttons

Date Pickers

Charts

Breadcrumbs

Pagination

Sidebar

Navbar

Footer

Widgets

Statistics Cards

Everything must be reusable.

---

# RESPONSIVENESS

Desktop First

Tablet

Mobile

Support:

320px+

No horizontal scrolling.

Responsive tables.

Responsive navigation.

Responsive dashboards.

---

# DASHBOARDS

Create dashboards for:

Super Admin

Admin

Project Manager

Field Manager

Field Agent

Client

Panelist

Each dashboard must show only relevant information.

---

# DATABASE

Generate complete migration files.

Generate seeders.

Generate factories.

Generate relationships.

Generate indexes.

Generate foreign keys.

Generate UUID support.

---

# REPORTING

Generate:

Excel

PDF

CSV

Printable Reports

PowerPoint-ready layouts

Charts

Executive summaries

---

# OFFLINE FIELD COLLECTION

Design architecture that supports future Progressive Web App (PWA) functionality.

Offline-first principles.

Background synchronization.

Conflict resolution.

GPS support.

Photo support.

Signature support.

---

# TESTING

Generate:

Feature Tests

Unit Tests

Authorization Tests

Validation Tests

Policy Tests

Critical business logic must be tested.

---

# DOCUMENTATION

Every module must include:

Purpose

Architecture

Database Tables

Relationships

Permissions

API Endpoints

Events

Jobs

Testing Requirements

Future Improvements

---

# DEVELOPMENT ORDER

Phase 1

Corporate Website

Authentication

RBAC

Admin Dashboard

Settings

Users

Roles

Permissions

---

Phase 2

CRM

Research Requests

Client Portal

Projects

---

Phase 3

Survey Builder

Survey Engine

Question Bank

Response Collection

---

Phase 4

Panel Management

Qualification Tests

Wallet

Rewards

Gamification

---

Phase 5

Field Operations

Offline Collection

GPS

Assignments

Sync

---

Phase 6

Reports

Analytics

Charts

AI Reporting

---

Phase 7

Marketplace

Academy

Public Opinion

Social Listening

---

# FINAL REQUIREMENT

Every piece of code must be production-ready, modular, secure, maintainable, scalable, and suitable for deployment on shared hosting today while remaining capable of migrating to VPS or cloud infrastructure without major architectural changes.
